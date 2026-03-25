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
            'body'            => 'nullable|string',
            'attachment'      => 'nullable|file|max:20480', // 20MB para S3 es razonable
            'parent_id'       => 'nullable|exists:int_messages,id' 
        ]);

        // Verificar seguridad
        $isParticipant = IntConversationParticipant::where('conversation_id', $validated['conversation_id'])
                            ->where('user_id', auth()->id())
                            ->exists();

        if (!$isParticipant) {
            return back()->with('error', 'No tienes permiso.');
        }

        $attachmentPath = null;
        if ($request->hasFile('attachment')) {
            // CAMBIO: Guardamos directamente en el disco S3
            $attachmentPath = $request->file('attachment')->store('chat_attachments', 's3');
        }

        if (empty($validated['body']) && !$attachmentPath) {
            return back();
        }

        IntMessage::create([
            'conversation_id' => $validated['conversation_id'],
            'user_id'         => auth()->id(),
            'body'            => $validated['body'] ?? '',
            'attachment'      => $attachmentPath, // Guardamos la ruta del bucket
            'parent_id'       => $validated['parent_id'] ?? null,
        ]);

        return back();
    }
}

