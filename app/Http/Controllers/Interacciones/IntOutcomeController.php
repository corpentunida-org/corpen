<?php

namespace App\Http\Controllers\Interacciones;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Interacciones\Concerns\GestionaAreaDeCatalogo;
use App\Models\Interacciones\IntOutcome;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class IntOutcomeController extends Controller
{
    use GestionaAreaDeCatalogo;

    /**
     * Mostrar lista de resultados de interacción
     */
    public function index(Request $request)
    {
        $puedeElegirArea = $this->puedeElegirArea();
        $miArea = $this->areaDelUsuario();

        // UX: Contamos interacciones y seguimientos para proteger la integridad
        $outcomes = IntOutcome::withCount(['interactions', 'seguimientos'])
            ->when(!$puedeElegirArea, fn ($q) => $q->where(fn ($q2) => $q2->whereNull('area')->orWhere('area', $miArea)))
            // Filtro por área: mismo criterio que Tipos — "compartido" pide area IS NULL.
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

        return view('interactions.outcomes.index', compact('outcomes', 'puedeElegirArea', 'areasDisponibles'));
    }

    /**
     * Mostrar formulario de creación
     */
    public function create()
    {
        $puedeElegirArea = $this->puedeElegirArea();
        $miArea = $this->areaDelUsuario();
        $areas = DB::table('roles')->whereNotNull('area')->distinct()->orderBy('area')->pluck('area');

        return view('interactions.outcomes.create', compact('puedeElegirArea', 'miArea', 'areas'));
    }

    /**
     * Guardar un nuevo resultado. Sin el permiso para elegir área, queda ligado automáticamente a
     * la del perfil de quien lo crea — mismo criterio que Tipos.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $area = $this->puedeElegirArea()
            ? ($request->input('area') ?: null)
            : $this->areaDelUsuario();

        IntOutcome::create([
            'name' => $request->name,
            'area' => $area,
        ]);

        return redirect()
            ->route('interactions.outcomes.index')
            ->with('success', 'Resultado creado exitosamente' . ($area ? ' para el área ' . strtoupper($area) : ' (compartido, para todas las áreas)') . '.');
    }

    /** Bloquea tocar el resultado de OTRA área si no se puede administrar cualquiera. */
    private function autorizarResultado(IntOutcome $outcome): void
    {
        if ($this->puedeElegirArea()) {
            return;
        }
        abort_unless($outcome->area === null || $outcome->area === $this->areaDelUsuario(), 404);
    }

    /**
     * Mostrar un resultado específico
     */
    public function show($id)
    {
        $outcome = IntOutcome::withCount(['interactions', 'seguimientos'])->findOrFail($id);
        $this->autorizarResultado($outcome);

        return view('interactions.outcomes.show', compact('outcome'));
    }

    /**
     * Mostrar formulario de edición
     */
    public function edit($id)
    {
        $outcome = IntOutcome::findOrFail($id);
        $this->autorizarResultado($outcome);

        $puedeElegirArea = $this->puedeElegirArea();
        $areas = DB::table('roles')->whereNotNull('area')->distinct()->orderBy('area')->pluck('area');

        return view('interactions.outcomes.edit', compact('outcome', 'puedeElegirArea', 'areas'));
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
        $this->autorizarResultado($outcome);

        $update = ['name' => $request->name];
        // El área solo se puede cambiar con el permiso de administrar cualquiera — quien solo
        // administra la suya no puede "mudar" un resultado a otra área ni volverlo compartido.
        if ($this->puedeElegirArea()) {
            $update['area'] = $request->input('area') ?: null;
        }
        $outcome->update($update);

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
        $this->autorizarResultado($outcome);

        // UX: Sumamos ambos conteos para la validación
        $totalRelaciones = $outcome->interactions_count + $outcome->seguimientos_count;

        if ($totalRelaciones > 0) {
            return redirect()->back()->with('error', "No se puede eliminar '{$outcome->name}'. Está siendo usado en {$outcome->interactions_count} interacciones y {$outcome->seguimientos_count} seguimientos.");
        }

        $outcome->delete();
        return redirect()->route('interactions.outcomes.index')->with('success', 'Resultado eliminado correctamente.');
    }
}
