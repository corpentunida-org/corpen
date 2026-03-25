<?php

namespace App\Http\Controllers\Interacciones;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Interacciones\IntWorkspace;
use App\Models\Interacciones\IntConversation;

class IntChatController extends Controller
{
    /**
     * Carga la vista principal del Centro de Chat
     */
    public function index(Request $request)
    {
        // 1. Cargar todos los espacios de trabajo para la Columna 1
        $workspaces = IntWorkspace::where('status', 'active')->get();
        
        $activeWorkspace = null;
        $activeConversation = null;
        $messages = [];

        // 2. Si el usuario hizo clic en un Workspace (Columna 2)
        if ($request->has('workspace_id')) {
            $activeWorkspace = IntWorkspace::with('conversations')->find($request->workspace_id);
        }

        // 3. Si el usuario hizo clic en un Chat específico (Columna 3)
        if ($request->has('chat_id')) {
            // Traemos el chat con sus mensajes y los usuarios que los enviaron
            $activeConversation = IntConversation::with(['messages.user'])->find($request->chat_id);
            
            if ($activeConversation) {
                $messages = $activeConversation->messages;
            }
        }

        // 4. Enviar todo a tu vista index.blade.php
        return view('interactions.chat.index', compact(
            'workspaces', 
            'activeWorkspace', 
            'activeConversation', 
            'messages'
        ));
    }
}