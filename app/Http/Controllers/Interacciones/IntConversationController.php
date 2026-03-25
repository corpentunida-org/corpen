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
            'role_id' => 'required_with:user_ids|exists:int_roles,id' // Obligatorio si se invitan usuarios
        ]);

        $conversation = \Illuminate\Support\Facades\DB::transaction(function () use ($validated) {
            
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

            // El creador de la sala siempre será 'admin'
            $adminRole = IntRole::where('slug', 'admin')->first();

            if ($adminRole) {
                IntConversationParticipant::create([
                    'conversation_id' => $chat->id,
                    'user_id' => auth()->id(), 
                    'role_id' => $adminRole->id,
                    'joined_at' => now(), // <-- FECHA EXACTA REQUERIDA POR TU MODELO
                ]);
            }

            // A los invitados se les asigna el rol que seleccionaste en el modal
            if (!empty($validated['user_ids']) && !empty($validated['role_id'])) {
                foreach ($validated['user_ids'] as $invitadoId) {
                    IntConversationParticipant::create([
                        'conversation_id' => $chat->id,
                        'user_id' => $invitadoId, 
                        'role_id' => $validated['role_id'], // <-- ROL SELECCIONADO
                        'joined_at' => now(), // <-- FECHA EXACTA
                    ]);
                }
            }

            return $chat;
        });

        return redirect()->route('interactions.chat.index', [
            'workspace_id' => $validated['workspace_id'],
            'chat_id' => $conversation->id
        ])->with('success', 'Sala creada exitosamente con sus participantes.');
    }
    /**
     * Caso de Uso: Agregar nuevos participantes a una sala existente
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
            // Verificar si el usuario ya está en esa sala
            $yaEsParticipante = IntConversationParticipant::where('conversation_id', $validated['conversation_id'])
                                    ->where('user_id', $userId)
                                    ->exists();

            if (!$yaEsParticipante) {
                IntConversationParticipant::create([
                    'conversation_id' => $validated['conversation_id'],
                    'user_id' => $userId,
                    'role_id' => $validated['role_id'],
                ]);
                $agregados++;
            }
        }

        if ($agregados > 0) {
            return back()->with('success', $agregados . ' participante(s) agregado(s) correctamente.');
        } else {
            return back()->with('info', 'Los usuarios seleccionados ya pertenecen a la sala.');
        }
    }
    public function removeParticipant(IntConversationParticipant $participant)
    {
        // 1. Seguridad: Verificar que el usuario que intenta eliminar sea ADMIN en esta sala
        $currentUserRole = IntConversationParticipant::where('conversation_id', $participant->conversation_id)
            ->where('user_id', auth()->id())
            ->first();

        if (!$currentUserRole || $currentUserRole->role->slug !== 'admin') {
            return back()->with('error', 'No tienes permisos de Administrador para quitar personas.');
        }

        // 2. No permitir que el Admin se elimine a sí mismo (opcional, por seguridad)
        if ($participant->user_id === auth()->id()) {
            return back()->with('error', 'No puedes eliminarte a ti mismo de la sala.');
        }

        // 3. Proceder con la eliminación
        $participant->delete();

        return back()->with('success', 'Participante removido de la sala.');
    }
}