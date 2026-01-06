<?php

namespace App\Http\Controllers\Flujo;

use App\Http\Controllers\Controller;
use App\Models\Flujo\Workflow;
use App\Models\Flujo\Task;
use App\Models\Flujo\TaskHistory;
use App\Models\Flujo\TaskComment;

class TableroController extends Controller
{
    public function index()
    {
        // Tu lógica original, que funciona perfectamente
        $workflows = Workflow::with('creator')->paginate(3);
        $tasks     = Task::with('user', 'workflow')->paginate(3);
        $histories = TaskHistory::with('task', 'user')->latest()->paginate(4);
        $comments  = TaskComment::with('task','user')->latest()->paginate(4);

        // --- ÚNICO CAMBIO: Añadir las variables que necesita la vista ---
        $estados = [
            'borrador' => 'Borrador',
            'activo' => 'Activo',
            'pausado' => 'Pausado',
            'completado' => 'Completado',
            'archivado' => 'Archivado'
        ];
        $prioridades = [
            'baja' => 'Baja',
            'media' => 'Media',
            'alta' => 'Alta',
            'crítica' => 'Crítica'
        ];

        // --- CAMBIO 2: Añadir las nuevas variables a compact() ---
        return view('flujo.index', compact('workflows', 'tasks', 'histories', 'comments', 'estados', 'prioridades'));
    }
}