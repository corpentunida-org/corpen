<?php

namespace App\Http\Controllers\Flujo;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Flujo\Task;
use App\Models\Flujo\Workflow;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class TaskController extends Controller
{
   
    /**
     * Listado de tareas
     */
    public function index()
    {
        $tasks = Task::with('user', 'workflow', 'histories', 'comments')->paginate(4);

        return view('flujo.tasks.index', compact('tasks'));
    }

    /**
     * Mostrar formulario para crear una nueva tarea
     */
    public function create()
    {
        $workflows = Workflow::all();
        $users = User::all();

        return view('flujo.tasks.create', compact('workflows', 'users'));
    }

    /**
     * Guardar una nueva tarea
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'titulo'       => 'required|string|max:255',
            'descripcion'  => 'nullable|string',
            'estado'       => 'required|in:pendiente,en_proceso,revisado,completado',
            'prioridad'    => 'required|in:baja,media,alta',
            'fecha_limite' => 'nullable|date',
            'user_id'      => 'required|exists:users,id',
            'workflow_id'  => 'nullable|exists:workflows,id',
        ]);

        Task::create($data);

        return redirect()->route('flujo.tasks.index')
            ->with('success', '✅ Tarea creada correctamente.');
    }

    /**
     * Ver detalle de una tarea
     */
    public function show(Task $task)
    {
        $task->load('user', 'workflow', 'histories', 'comments');

        return view('flujo.tasks.show', compact('task'));
    }
    public function edit(Task $task)
    {
        $workflows = Workflow::all();
        $users = User::all();

        $task->load('comments.user'); // ⚡ importante

        return view('flujo.tasks.edit', compact('task', 'workflows', 'users'));
    }

    public function update(Request $request, Task $task)
    {
        $data = $request->validate([
            'titulo'       => 'required|string|max:255',
            'descripcion'  => 'nullable|string',
            'estado'       => 'required|in:pendiente,en_proceso,revisado,completado',
            'prioridad'    => 'required|in:baja,media,alta',
            'fecha_limite' => 'nullable|date',
            'user_id'      => 'required|exists:users,id',
            'workflow_id'  => 'nullable|exists:workflows,id',
        ]);

        // Guardar estado anterior
        $estadoAnterior = $task->estado;

        // Actualizar la tarea
        $task->update($data);

        // Si el estado cambió, registrar en el historial
        if ($estadoAnterior !== $data['estado']) {
            \App\Models\Flujo\TaskHistory::create([
                'task_id'         => $task->id,
                'estado_anterior' => $estadoAnterior,
                'estado_nuevo'    => $data['estado'],
                'cambiado_por'    => Auth::id(),
                'fecha_cambio'    => now(),
            ]);
        }

        return redirect()->route('flujo.tasks.index')
            ->with('success', '✏️ Tarea actualizada correctamente.');
    }
    public function destroy(Task $task)
    {
        $task->delete();

        return redirect()->route('flujo.tasks.index')
            ->with('success', '🗑️ Tarea eliminada correctamente.');
    }
}
