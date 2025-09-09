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
        // Paginación: 10 elementos por página (puedes ajustar el número)
        $workflows = Workflow::with('creator')->paginate(3);
        $tasks     = Task::with('user', 'workflow')->paginate(3);
        $histories = TaskHistory::with('task', 'user')->latest()->paginate(4);
        $comments  = TaskComment::with('task','user')->latest()->paginate(4);

        return view('flujo.index', compact('workflows', 'tasks', 'histories', 'comments'));
    }
}
