<?php

namespace App\Http\Controllers\Flujo;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Flujo\Workflow;
use App\Models\User;

class WorkflowController extends Controller
{
    /**
     * Listado de workflows
     */
    public function index()
    {
        $workflows = Workflow::with('creator')->paginate(10);

        return view('flujo.workflows.index', compact('workflows'));
    }

    /**
     * Mostrar formulario para crear un workflow
     */
    public function create()
    {
        $users = User::all(); // Para seleccionar el creador

        return view('flujo.workflows.create', compact('users'));
    }

    /**
     * Guardar un nuevo workflow
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'nombre'      => 'required|string|max:255',
            'descripcion' => 'nullable|string',
            'creado_por'  => 'required|exists:users,id',
        ]);

        Workflow::create($data);

        return redirect()->route('flujo.workflows.index')
            ->with('success', '✅ Workflow creado correctamente.');
    }

    /**
     * Mostrar detalle de un workflow
     */
    public function show(Workflow $workflow)
    {
        $workflow->load('creator', 'tasks');

        return view('flujo.workflows.show', compact('workflow'));
    }

    /**
     * Mostrar formulario para editar un workflow
     */
    public function edit(Workflow $workflow)
    {
        $users = User::all();

        return view('flujo.workflows.edit', compact('workflow', 'users'));
    }

    /**
     * Actualizar un workflow
     */
    public function update(Request $request, Workflow $workflow)
    {
        $data = $request->validate([
            'nombre'      => 'required|string|max:255',
            'descripcion' => 'nullable|string',
            'creado_por'  => 'required|exists:users,id',
        ]);

        $workflow->update($data);

        return redirect()->route('flujo.workflows.index')
            ->with('success', '✏️ Workflow actualizado correctamente.');
    }

    /**
     * Eliminar un workflow
     */
    public function destroy(Workflow $workflow)
    {
        $workflow->delete();

        return redirect()->route('flujo.workflows.index')
            ->with('success', '🗑️ Workflow eliminado correctamente.');
    }
}
