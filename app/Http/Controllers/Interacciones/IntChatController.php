<?php

namespace App\Http\Controllers\Interacciones;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Interacciones\IntWorkspace;
use App\Models\Interacciones\IntConversation;

class IntChatController extends Controller
{
    /**
     * Carga la vista principal del Centro de Chat con soporte para 
     * Workspaces, Conversaciones, Mensajes y Participantes con Roles.
     */
    public function index(Request $request)
    {
        // 1. Cargar todos los espacios de trabajo activos (Columna 1)
        $workspaces = IntWorkspace::where('status', 'active')->get();
        
        $activeWorkspace = null;
        $activeConversation = null;
        $messages = [];
        $participants = [];

        // 2. Si el usuario seleccionó un Workspace (Columna 2)
        if ($request->has('workspace_id')) {
            // Cargamos el workspace con sus conversaciones para listarlas
            $activeWorkspace = IntWorkspace::with('conversations')->find($request->workspace_id);
        }

        // 3. Si el usuario seleccionó una Sala específica (Columna 3)
        if ($request->has('chat_id')) {
            // Traemos la conversación con:
            // - messages.user: para saber quién envió cada mensaje
            // - participants.user: para la lista de invitados
            // - participants.role: para saber el rol (Admin, Asesor, etc.) de cada invitado
            $activeConversation = IntConversation::with([
                'messages.user', 
                'participants.user', 
                'participants.role'
            ])->find($request->chat_id);
            
            if ($activeConversation) {
                // Seteamos los mensajes para la ventana de chat
                $messages = $activeConversation->messages;
                
                // Seteamos los participantes para ver los datos de los invitados
                $participants = $activeConversation->participants;
            }
        }

        // 4. Enviar todas las variables necesarias a la vista
        return view('interactions.chat.index', compact(
            'workspaces', 
            'activeWorkspace', 
            'activeConversation', 
            'messages',
            'participants' // Lista de invitados con sus roles
        ));
    }
}