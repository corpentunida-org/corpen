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
        // 1. Añadimos 'user_ids' a la validación
        $validated = $request->validate([
            'workspace_id' => 'required|exists:int_workspaces,id',
            'name' => 'required|string|max:255',
            'visibility' => 'required|in:internal,external',
            'chatable_type' => 'nullable|string', 
            'chatable_id' => 'nullable|integer',
            'user_ids' => 'nullable|array', // Es un array de IDs
            'user_ids.*' => 'exists:users,id' // Verificamos que los usuarios existan
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

            // Obtener roles
            $adminRole = IntRole::where('slug', 'admin')->first();
            // Asumimos que tienes un rol 'empleado' o 'miembro' en tu tabla int_roles
            $empleadoRole = IntRole::where('slug', 'empleado')->first() ?? $adminRole; 

            // 1. Vincular al creador como ADMIN
            if ($adminRole) {
                IntConversationParticipant::create([
                    'conversation_id' => $chat->id,
                    'user_id' => auth()->id(), 
                    'role_id' => $adminRole->id, 
                ]);
            }

            // 2. Vincular a los INVITADOS que el creador seleccionó en el modal
            if (!empty($validated['user_ids'])) {
                foreach ($validated['user_ids'] as $invitadoId) {
                    IntConversationParticipant::create([
                        'conversation_id' => $chat->id,
                        'user_id' => $invitadoId, 
                        'role_id' => $empleadoRole->id, // Entran como rol normal, no admin
                    ]);
                }
            }

            return $chat;
        });

        return redirect()->route('interactions.chat.index', [
            'workspace_id' => $validated['workspace_id'],
            'chat_id' => $conversation->id
        ])->with('success', 'Sala creada e invitados agregados exitosamente.');
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
}