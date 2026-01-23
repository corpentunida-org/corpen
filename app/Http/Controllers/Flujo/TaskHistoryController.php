<?php

namespace App\Http\Controllers\Flujo;

use App\Http\Controllers\Controller;
use App\Models\Flujo\TaskHistory;
use Illuminate\Http\Request;

class TaskHistoryController extends Controller
{
    /**
     * Listado de Auditoría para la Vista Web
     */
    public function index()
    {
        // 1. CARGA ANIDADA: Cargamos la tarea y el workflow de esa tarea
        // 2. PAGINACIÓN: Cambiamos get() por paginate() para que funcione $histories->links()
        $histories = TaskHistory::with(['user', 'task.workflow'])
            ->orderByDesc('fecha_cambio')
            ->paginate(10);

        // Si la petición pide JSON (ej: una llamada AJAX desde el dashboard)
        if (request()->wantsJson()) {
            return response()->json($histories);
        }

        // Retornamos la vista (ajusta la ruta según tu estructura)
        return view('flujo.history.index', compact('histories'));
    }

    public function show(TaskHistory $taskHistory)
    {
        // Cargamos la trazabilidad completa para el detalle
        $taskHistory->load(['task.workflow', 'user']);
        return response()->json($taskHistory);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'task_id'         => 'required|exists:wor_tasks,id',
            'estado_anterior' => 'nullable|string',
            'estado_nuevo'    => 'nullable|string',
            'cambiado_por'    => 'required|exists:users,id',
        ]);

        $data['fecha_cambio'] = now();

        $history = TaskHistory::create($data);

        // Si es una acción web, solemos redirigir atrás
        if (!request()->wantsJson()) {
            return back()->with('success', 'Evento de auditoría registrado.');
        }

        return response()->json($history, 201);
    }

    public function destroy(TaskHistory $taskHistory)
    {
        $taskHistory->delete();
        
        if (!request()->wantsJson()) {
            return back()->with('success', 'Registro eliminado permanentemente.');
        }

        return response()->json(['message' => 'Historial eliminado']);
    }
}