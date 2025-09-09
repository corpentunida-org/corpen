<?php

namespace App\Http\Controllers\Flujo;

use App\Http\Controllers\Controller;
use App\Models\Flujo\TaskHistory;
use Illuminate\Http\Request;

class TaskHistoryController extends Controller
{
    public function index()
    {
        $histories = TaskHistory::with('task','user')->orderByDesc('fecha_cambio')->get();
        return response()->json($histories);
    }

    public function show(TaskHistory $taskHistory)
    {
        $taskHistory->load('task','user');
        return response()->json($taskHistory);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'task_id' => 'required|exists:tasks,id',
            'estado_anterior' => 'nullable|string',
            'estado_nuevo' => 'nullable|string',
            'cambiado_por' => 'required|exists:users,id',
        ]);

        $data['fecha_cambio'] = now();

        $history = TaskHistory::create($data);

        return response()->json($history, 201);
    }

    public function destroy(TaskHistory $taskHistory)
    {
        $taskHistory->delete();
        return response()->json(['message' => 'Historial eliminado']);
    }
}
