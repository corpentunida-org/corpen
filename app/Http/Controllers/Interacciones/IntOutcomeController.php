<?php

namespace App\Http\Controllers\Interacciones;

use App\Http\Controllers\Controller;
use App\Models\Interacciones\IntOutcome;
use Illuminate\Http\Request;

class IntOutcomeController extends Controller
{
    /**
     * Mostrar lista de resultados de interacción
     */
    public function index(Request $request)
    {
        // UX: Contamos interacciones y seguimientos para proteger la integridad
        $outcomes = IntOutcome::withCount(['interactions', 'seguimientos'])
            ->when($request->search, function($query, $search) {
                $query->where('name', 'like', "%{$search}%");
            })
            ->orderBy('name', 'asc')
            ->paginate(12)
            ->withQueryString();

        return view('interactions.outcomes.index', compact('outcomes'));
    }

    /**
     * Mostrar formulario de creación
     */
    public function create()
    {
        return view('interactions.outcomes.create');
    }

    /**
     * Guardar un nuevo resultado
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
        ]);

        IntOutcome::create([
            'name' => $request->name,
        ]);

        return redirect()
            ->route('interactions.outcomes.index')
            ->with('success', 'Resultado creado exitosamente');
    }

    /**
     * Mostrar un resultado específico
     */
    public function show($id)
    {
        $outcome = IntOutcome::withCount(['interactions', 'seguimientos'])
            ->findOrFail($id);

        return view('interactions.outcomes.show', compact('outcome'));
    }

    /**
     * Mostrar formulario de edición
     */
    public function edit($id)
    {
        $outcome = IntOutcome::findOrFail($id);
        return view('interactions.outcomes.edit', compact('outcome'));
    }

    /**
     * Actualizar un resultado
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $outcome = IntOutcome::findOrFail($id);
        $outcome->update([
            'name' => $request->name,
        ]);

        return redirect()
            ->route('interactions.outcomes.index')
            ->with('success', 'Resultado actualizado correctamente');
    }

    /**
     * Eliminar un resultado
     */
    public function destroy($id)
    {
        $outcome = IntOutcome::withCount(['interactions', 'seguimientos'])->findOrFail($id);

        // UX: Sumamos ambos conteos para la validación
        $totalRelaciones = $outcome->interactions_count + $outcome->seguimientos_count;

        if ($totalRelaciones > 0) {
            return redirect()->back()->with('error', "No se puede eliminar '{$outcome->name}'. Está siendo usado en {$outcome->interactions_count} interacciones y {$outcome->seguimientos_count} seguimientos.");
        }

        $outcome->delete();
        return redirect()->route('interactions.outcomes.index')->with('success', 'Resultado eliminado correctamente.');
    }
}
