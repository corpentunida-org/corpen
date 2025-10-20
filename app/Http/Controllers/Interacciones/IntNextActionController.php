<?php

namespace App\Http\Controllers\Interacciones;

use App\Http\Controllers\Controller;
use App\Models\Interacciones\IntNextAction;
use Illuminate\Http\Request;

class IntNextActionController extends Controller
{
    /**
     * Mostrar lista de próximas acciones
     */
    public function index()
    {
        $nextActions = IntNextAction::paginate(10);
        return view('interactions.next_actions.index', compact('nextActions'));
    }

    /**
     * Mostrar formulario de creación
     */
    public function create()
    {
        return view('interactions.next_actions.create');
    }

    /**
     * Guardar una nueva próxima acción
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
        ]);

        IntNextAction::create([
            'name' => $request->name,
        ]);

        return redirect()
            ->route('interactions.next_actions.index')
            ->with('success', 'Próxima acción creada exitosamente');
    }

    /**
     * Mostrar una próxima acción específica
     */
    public function show($id)
    {
        $action = IntNextAction::findOrFail($id);
        return view('interactions.next_actions.show', compact('action'));
    }

    /**
     * Mostrar formulario de edición
     */
    public function edit($id)
    {
        $action = IntNextAction::findOrFail($id);
        return view('interactions.next_actions.edit', compact('action'));
    }

    /**
     * Actualizar una próxima acción
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $action = IntNextAction::findOrFail($id);
        $action->update([
            'name' => $request->name,
        ]);

        return redirect()
            ->route('interactions.next_actions.index')
            ->with('success', 'Próxima acción actualizada correctamente');
    }

    /**
     * Eliminar una próxima acción
     */
    public function destroy($id)
    {
        $action = IntNextAction::findOrFail($id);
        $action->delete();

        return redirect()
            ->route('interactions.next_actions.index')
            ->with('success', 'Próxima acción eliminada correctamente');
    }
}
