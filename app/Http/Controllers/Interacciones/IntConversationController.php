<?php

namespace App\Http\Controllers\Interacciones;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Interacciones\IntConversation;
use App\Models\Interacciones\IntConversationParticipant;
use App\Models\Interacciones\IntRole;

class IntConversationController extends Controller
{
    /**
     * Crear una sala contextual vinculada a un modelo (Seguimiento, Interacción, etc.)
     */
    public function storeContextual(Request $request)
    {
        $validated = $request->validate([
            'workspace_id' => 'required|exists:int_workspaces,id',
            'name' => 'required|string|max:255',
            'visibility' => 'required|in:internal,external',
            'chatable_type' => 'nullable|string', 
            'chatable_id' => 'nullable|integer',
            'user_ids' => 'nullable|array',
            'user_ids.*' => 'exists:users,id',
            'role_id' => 'required_with:user_ids|exists:int_roles,id' 
        ]);

        $conversation = DB::transaction(function () use ($validated) {
            
            $type = (!empty($validated['chatable_type']) && !empty($validated['chatable_id'])) 
                    ? 'contextual' 
                    : 'general';

            $chat = IntConversation::create([
                'workspace_id' => $validated['workspace_id'],
                'type' => $type,
                'visibility' => $validated['visibility'],
                'name' => $validated['name'],
                'chatable_type' => $validated['chatable_type'] ?? null,
                'chatable_id' => $validated['chatable_id'] ?? null,
            ]);

            $adminRole = IntRole::where('slug', 'admin')->first();

            if ($adminRole) {
                IntConversationParticipant::create([
                    'conversation_id' => $chat->id,
                    'user_id' => auth()->id(), 
                    'role_id' => $adminRole->id,
                    'joined_at' => now(),
                ]);
            }

            if (!empty($validated['user_ids']) && !empty($validated['role_id'])) {
                foreach ($validated['user_ids'] as $invitadoId) {
                    IntConversationParticipant::create([
                        'conversation_id' => $chat->id,
                        'user_id' => $invitadoId, 
                        'role_id' => $validated['role_id'],
                        'joined_at' => now(),
                    ]);
                }
            }

            return $chat;
        });

        return redirect()->route('interactions.chat.index', [
            'workspace_id' => $validated['workspace_id'],
            'chat_id' => $conversation->id
        ])->with('success', 'Sala creada exitosamente.');
    }

    /**
     * Caso de Uso: Iniciar o recuperar un chat privado entre dos personas (Messenger Style)
     */
    public function iniciarChatPrivado(Request $request)
    {
        $validated = $request->validate([
            'target_user_id' => 'required|exists:users,id'
        ]);

        $miId = auth()->id();
        $suId = $validated['target_user_id'];

        if ($miId == $suId) {
            return back()->with('error', 'No puedes iniciar un chat privado contigo mismo.');
        }

        // 1. Buscar si ya existe una conversación privada entre estos dos usuarios en el Workspace 1
        $conversacion = IntConversation::where('type', 'private')
            ->where('workspace_id', 1) 
            ->whereHas('participants', function ($q) use ($miId) {
                $q->where('user_id', $miId);
            })
            ->whereHas('participants', function ($q) use ($suId) {
                $q->where('user_id', $suId);
            })
            ->first();

        // 2. Si existe, redirigir directamente
        if ($conversacion) {
            return redirect()->route('interactions.chat.index', [
                'workspace_id' => $conversacion->workspace_id,
                'chat_id' => $conversacion->id
            ]);
        }

        // 3. Si no existe, crearla en una transacción
        $nuevaSala = DB::transaction(function () use ($miId, $suId) {
            $chat = IntConversation::create([
                'workspace_id' => 4, // Workspace dedicado a DMs
                'type' => 'private',
                'visibility' => 'internal',
                'name' => null, // El nombre se maneja dinámicamente en la vista
            ]);

            $adminRole = IntRole::where('slug', 'admin')->first();

            if ($adminRole) {
                // Agregar a ambos como administradores de su conversación privada
                IntConversationParticipant::create([
                    'conversation_id' => $chat->id,
                    'user_id' => $miId,
                    'role_id' => $adminRole->id,
                    'joined_at' => now(),
                ]);

                IntConversationParticipant::create([
                    'conversation_id' => $chat->id,
                    'user_id' => $suId,
                    'role_id' => $adminRole->id,
                    'joined_at' => now(),
                ]);
            }

            return $chat;
        });

        return redirect()->route('interactions.chat.index', [
            'workspace_id' => 1,
            'chat_id' => $nuevaSala->id
        ])->with('success', 'Conversación privada iniciada.');
    }

    /**
     * Agregar nuevos participantes a una sala existente
     */
    public function addParticipants(Request $request)
    {
        $validated = $request->validate([
            'conversation_id' => 'required|exists:int_conversations,id',
            'user_ids' => 'required|array',
            'user_ids.*' => 'exists:users,id',
            'role_id' => 'required|exists:int_roles,id'
        ]);

        $agregados = 0;

        foreach ($validated['user_ids'] as $userId) {
            $yaEsParticipante = IntConversationParticipant::where('conversation_id', $validated['conversation_id'])
                                    ->where('user_id', $userId)
                                    ->exists();

            if (!$yaEsParticipante) {
                IntConversationParticipant::create([
                    'conversation_id' => $validated['conversation_id'],
                    'user_id' => $userId,
                    'role_id' => $validated['role_id'],
                    'joined_at' => now(),
                ]);
                $agregados++;
            }
        }

        return $agregados > 0 
            ? back()->with('success', $agregados . ' participante(s) agregado(s).')
            : back()->with('info', 'Los usuarios ya pertenecen a la sala.');
    }

    /**
     * Eliminar participante de una sala
     */
    public function removeParticipant(IntConversationParticipant $participant)
    {
        $currentUserRole = IntConversationParticipant::where('conversation_id', $participant->conversation_id)
            ->where('user_id', auth()->id())
            ->first();

        if (!$currentUserRole || $currentUserRole->role->slug !== 'admin') {
            return back()->with('error', 'No tienes permisos de Administrador.');
        }

        if ($participant->user_id === auth()->id()) {
            return back()->with('error', 'No puedes eliminarte a ti mismo.');
        }

        $participant->delete();

        return back()->with('success', 'Participante removido.');
    }
}