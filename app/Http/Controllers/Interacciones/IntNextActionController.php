<?php

namespace App\Http\Controllers\Interacciones;

use App\Http\Controllers\Controller;
use App\Models\Interacciones\IntNextAction;
use Illuminate\Http\Request;

class IntNextActionController extends Controller
{
    /**
     * Mostrar lista de próximas acciones (Vista SaaS Pro)
     */
    public function index(Request $request)
    {
        // Mantenemos solo seguimientos para evitar error SQL en tabla interactions
        $nextActions = IntNextAction::withCount('seguimientos') 
            ->when($request->search, function($query, $search) {
                $query->where('name', 'like', "%{$search}%");
            })
            ->orderBy('name', 'asc')
            ->paginate(12);

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
     * Mostrar detalle (Mantenemos variable $nextAction y withCount)
     */
    public function show($id)
    {
        $nextAction = IntNextAction::withCount('seguimientos')->findOrFail($id);
        
        return view('interactions.next_actions.show', compact('nextAction'));
    }

    /**
     * Formulario de edición (Estandarizado a variable $nextAction)
     */
    public function edit($id)
    {
        $nextAction = IntNextAction::findOrFail($id);
        return view('interactions.next_actions.edit', compact('nextAction'));
    }

    /**
     * Actualizar registro (Estandarizado a variable $nextAction)
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $nextAction = IntNextAction::findOrFail($id);
        $nextAction->update([
            'name' => $request->name,
        ]);

        return redirect()
            ->route('interactions.next_actions.index')
            ->with('success', 'Próxima acción actualizada correctamente');
    }

    /**
     * Eliminar registro (Mantenemos lógica de protección de integridad)
     */
    public function destroy($id)
    {
        // Solo verificamos seguimientos_count para evitar el error de columna inexistente
        $nextAction = IntNextAction::withCount('seguimientos')->findOrFail($id);

        if ($nextAction->seguimientos_count > 0) {
            return redirect()->back()->with('error', "No se puede eliminar: esta acción está programada en {$nextAction->seguimientos_count} seguimientos.");
        }

        $nextAction->delete();
        return redirect()->route('interactions.next_actions.index')->with('success', 'Eliminado correctamente.');
    }
}