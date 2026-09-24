<?php

namespace App\Http\Controllers\Interacciones;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Interacciones\Concerns\GestionaAreaDeCatalogo;
use App\Models\Interacciones\IntNextAction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class IntNextActionController extends Controller
{
    use GestionaAreaDeCatalogo;

    /**
     * Mostrar lista de próximas acciones (Vista SaaS Pro)
     */
    public function index(Request $request)
    {
        $puedeElegirArea = $this->puedeElegirArea();
        $miArea = $this->areaDelUsuario();

        // Mantenemos solo seguimientos para evitar error SQL en tabla interactions
        $nextActions = IntNextAction::withCount('seguimientos')
            ->when(!$puedeElegirArea, fn ($q) => $q->where(fn ($q2) => $q2->whereNull('area')->orWhere('area', $miArea)))
            // Filtro por área: mismo criterio que Tipos/Resultados — "compartido" pide area IS NULL.
            ->when($puedeElegirArea && $request->filled('area'), function ($q) use ($request) {
                $request->input('area') === 'compartido'
                    ? $q->whereNull('area')
                    : $q->where('area', $request->input('area'));
            })
            ->when($request->search, function($query, $search) {
                $query->where('name', 'like', "%{$search}%");
            })
            ->orderBy('name', 'asc')
            ->paginate(12)
            ->withQueryString();

        $areasDisponibles = $puedeElegirArea
            ? DB::table('roles')->whereNotNull('area')->distinct()->orderBy('area')->pluck('area')
            : collect();

        return view('interactions.next_actions.index', compact('nextActions', 'puedeElegirArea', 'areasDisponibles'));
    }

    /**
     * Mostrar formulario de creación
     */
    public function create()
    {
        $puedeElegirArea = $this->puedeElegirArea();
        $miArea = $this->areaDelUsuario();
        $areas = DB::table('roles')->whereNotNull('area')->distinct()->orderBy('area')->pluck('area');

        return view('interactions.next_actions.create', compact('puedeElegirArea', 'miArea', 'areas'));
    }

    /**
     * Guardar una nueva próxima acción. Sin el permiso para elegir área, queda ligada
     * automáticamente a la del perfil de quien la crea — mismo criterio que Tipos/Resultados.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $area = $this->puedeElegirArea()
            ? ($request->input('area') ?: null)
            : $this->areaDelUsuario();

        IntNextAction::create([
            'name' => $request->name,
            'area' => $area,
        ]);

        return redirect()
            ->route('interactions.next_actions.index')
            ->with('success', 'Próxima acción creada exitosamente' . ($area ? ' para el área ' . strtoupper($area) : ' (compartida, para todas las áreas)') . '.');
    }

    /** Bloquea tocar la acción de OTRA área si no se puede administrar cualquiera. */
    private function autorizarAccion(IntNextAction $nextAction): void
    {
        if ($this->puedeElegirArea()) {
            return;
        }
        abort_unless($nextAction->area === null || $nextAction->area === $this->areaDelUsuario(), 404);
    }

    /**
     * Mostrar detalle (Mantenemos variable $nextAction y withCount)
     */
    public function show($id)
    {
        $nextAction = IntNextAction::withCount('seguimientos')->findOrFail($id);
        $this->autorizarAccion($nextAction);

        return view('interactions.next_actions.show', compact('nextAction'));
    }

    /**
     * Formulario de edición (Estandarizado a variable $nextAction)
     */
    public function edit($id)
    {
        $nextAction = IntNextAction::findOrFail($id);
        $this->autorizarAccion($nextAction);

        $puedeElegirArea = $this->puedeElegirArea();
        $areas = DB::table('roles')->whereNotNull('area')->distinct()->orderBy('area')->pluck('area');

        return view('interactions.next_actions.edit', compact('nextAction', 'puedeElegirArea', 'areas'));
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
        $this->autorizarAccion($nextAction);

        $update = ['name' => $request->name];
        // El área solo se puede cambiar con el permiso de administrar cualquiera — quien solo
        // administra la suya no puede "mudar" una acción a otra área ni volverla compartida.
        if ($this->puedeElegirArea()) {
            $update['area'] = $request->input('area') ?: null;
        }
        $nextAction->update($update);

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
        $this->autorizarAccion($nextAction);

        if ($nextAction->seguimientos_count > 0) {
            return redirect()->back()->with('error', "No se puede eliminar: esta acción está programada en {$nextAction->seguimientos_count} seguimientos.");
        }

        $nextAction->delete();
        return redirect()->route('interactions.next_actions.index')->with('success', 'Eliminado correctamente.');
    }
}
