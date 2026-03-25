<?php

namespace App\Http\Controllers\Interacciones;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Interacciones\IntMessage;
use App\Models\Interacciones\IntConversationParticipant;

class IntMessageController extends Controller
{
    /**
     * Caso de Uso 3: Enviar un Mensaje (Normal o en Hilo)
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'conversation_id' => 'required|exists:int_conversations,id',
            'body' => 'required|string',
            'attachment' => 'nullable|string',
            'parent_id' => 'nullable|exists:int_messages,id' // Para responder mensajes (Hilos)
        ]);

        // Seguridad: Verificar que el usuario pertenezca a este chat antes de dejarlo escribir
        $isParticipant = IntConversationParticipant::where('conversation_id', $validated['conversation_id'])
                            ->where('user_id', auth()->id())
                            ->exists();

        if (!$isParticipant) {
            // REDIRECCIÓN WEB: Devuelve al usuario a la vista anterior con un mensaje de error
            return back()->with('error', 'No tienes permiso para escribir en este chat.');
        }

        // Crear el mensaje
        $message = IntMessage::create([
            'conversation_id' => $validated['conversation_id'],
            'user_id' => auth()->id(), // Automático: Quién lo envía
            'body' => $validated['body'],
            'attachment' => $validated['attachment'] ?? null,
            'parent_id' => $validated['parent_id'] ?? null, // Automático/Manual: Si es un hilo
        ]);

        // (Opcional) Aquí podrías actualizar el campo 'updated_at' de la conversación 
        // para que suba al principio de la lista de chats recientes.

        /* return response()->json([
            'message' => 'Mensaje enviado.',
            'data' => $message
        ], 201); */

        // REDIRECCIÓN WEB: Recarga la página actual de la sala para que aparezca el nuevo mensaje
        return back();
    }
}

