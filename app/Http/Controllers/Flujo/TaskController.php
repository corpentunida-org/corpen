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
     * Listado de tareas con búsqueda y filtros AJAX
     */
    public function index(Request $request)
    {
        // 1. Iniciamos la query con relaciones para evitar el problema N+1
        $query = Task::with(['user', 'workflow', 'histories', 'comments']);

        // 2. BUSCADOR (Título o Descripción)
        if ($request->filled('search')) {
            $query->where(function($q) use ($request) {
                $q->where('titulo', 'like', '%' . $request->search . '%')
                  ->orWhere('descripcion', 'like', '%' . $request->search . '%');
            });
        }

        // 3. FILTROS DINÁMICOS
        if ($request->filled('estado')) {
            $query->where('estado', $request->estado);
        }

        if ($request->filled('prioridad')) {
            $query->where('prioridad', $request->prioridad);
        }

        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        // 4. Ejecución de la consulta con paginación
        $tasks = $query->latest()->paginate(10);

        // 5. Datos necesarios para los selects de los filtros (Soluciona el error Undefined variable $users)
        $users = User::select('id', 'name')->orderBy('name')->get();

        // 6. LÓGICA AJAX: Retorna solo el fragmento de la tabla si es una petición asíncrona
        if ($request->ajax()) {
            return view('flujo.tasks.index', compact('tasks', 'users'))
                ->fragment('tasks-list');
        }

        return view('flujo.tasks.index', compact('tasks', 'users'));
    }

    /**
     * Mostrar formulario para crear una nueva tarea
     */
    public function create()
    {
        $workflows = Workflow::all();
        $users = User::select('id', 'name')->orderBy('name')->get();

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
        $task->load(['user', 'workflow', 'histories.user', 'comments.user']);

        return view('flujo.tasks.show', compact('task'));
    }

    /**
     * Mostrar formulario de edición
     */
    public function edit(Task $task)
    {
        $workflows = Workflow::all();
        $users = User::select('id', 'name')->orderBy('name')->get();

        $task->load('comments.user');

        return view('flujo.tasks.edit', compact('task', 'workflows', 'users'));
    }

    /**
     * Actualizar una tarea y registrar historial
     */
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

        $estadoAnterior = $task->estado;
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

    /**
     * Eliminar una tarea
     */
    public function destroy(Task $task)
    {
        $task->delete();

        return redirect()->route('flujo.tasks.index')
            ->with('success', '🗑️ Tarea eliminada correctamente.');
    }
}