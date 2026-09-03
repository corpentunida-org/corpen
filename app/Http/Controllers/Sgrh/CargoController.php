<?php

namespace App\Http\Controllers\Sgrh;

use App\Http\Controllers\AuditoriaController;
use App\Http\Controllers\Controller;
use App\Models\Sgrh\Area;
use App\Models\Sgrh\Cargo;
use Illuminate\Http\Request;

class CargoController extends Controller
{
    private function auditoria($accion)
    {
        app(AuditoriaController::class)->create($accion, 'SGRH');
    }

    // Jornada: lista fija, no amerita una tabla catálogo aparte (mismo criterio que sexo/tip_pers).
    private const JORNADAS = ['Tiempo completo', 'Medio tiempo', 'Por horas'];

    public function index(Request $request)
    {
        // Eager load: evita una consulta a sgrh_areas por fila listada (N+1); igual con
        // empleados_count, que se resuelve en un solo COUNT() en vez de uno por cargo.
        // Cuenta contratos activos con este cargo (no Empleado.cargo_id directo, que ya no es
        // una columna real) — como un colaborador tiene a lo sumo un contrato activo, equivale
        // a "colaboradores actuales en este cargo".
        $query = Cargo::with('area')->withCount(['contratos as empleados_count' => function ($q) {
            $q->where('estado', 'Activo');
        }]);

        if ($request->filled('search')) {
            $query->where('nombre', 'like', "%{$request->search}%");
        }

        if ($request->filled('area_id')) {
            $query->where('sgrh_area_id', $request->area_id);
        }

        $cargos = $query->orderBy('nombre')->paginate(20)->appends($request->query());
        $areas = Area::where('activo', true)->orderBy('nombre')->get();

        return view('sgrh.cargo.index', compact('cargos', 'areas'));
    }

    public function create()
    {
        $areas = Area::where('activo', true)->orderBy('nombre')->get();
        $jornadas = self::JORNADAS;
        $cargos = Cargo::where('activo', true)->orderBy('nombre')->get();

        return view('sgrh.cargo.create', compact('areas', 'jornadas', 'cargos'));
    }

    public function store(Request $request)
    {
        $validated = $this->validado($request);
        $validated['activo'] = $request->boolean('activo', true);

        $cargo = Cargo::create($validated);

        $this->auditoria("Cargo creado: {$cargo->nombre} (id {$cargo->id})");

        return redirect()->route('sgrh.cargo.index')->with('success', 'Cargo creado correctamente.');
    }

    public function edit(Cargo $cargo)
    {
        $areas = Area::where('activo', true)->orderBy('nombre')->get();
        $jornadas = self::JORNADAS;
        // Un cargo no puede ser su propio jefe inmediato/director: se excluye de las opciones.
        $cargos = Cargo::where('activo', true)->where('id', '!=', $cargo->id)->orderBy('nombre')->get();

        return view('sgrh.cargo.edit', compact('cargo', 'areas', 'jornadas', 'cargos'));
    }

    public function update(Request $request, Cargo $cargo)
    {
        $validated = $this->validado($request);
        $validated['activo'] = $request->boolean('activo', true);

        if (($validated['jefe_inmediato_id'] ?? null) == $cargo->id || ($validated['director_id'] ?? null) == $cargo->id) {
            return back()->withInput()->with('error', 'Un cargo no puede ser su propio jefe inmediato o director.');
        }

        $cargo->update($validated);

        $this->auditoria("Cargo actualizado: {$cargo->nombre} (id {$cargo->id})");

        return redirect()->route('sgrh.cargo.index')->with('success', 'Cargo actualizado correctamente.');
    }

    public function destroy(Cargo $cargo)
    {
        // Guardia de aplicación: sgrh_contratos.cargo_id es nullOnDelete, así que sin este
        // chequeo el cargo se borraría igual y solo dejaría esos contratos sin cargo. Se
        // revisa por contrato ACTIVO (no cualquier contrato histórico con este cargo — un
        // cargo descontinuado con solo contratos ya cerrados sí debe poder eliminarse).
        if ($cargo->contratos()->where('estado', 'Activo')->exists()) {
            return back()->with('error', 'No se puede eliminar el cargo porque tiene colaboradores con contrato activo en él. Reasígnalos primero.');
        }

        $nombre = $cargo->nombre;
        $cargo->delete();

        $this->auditoria("Cargo eliminado: {$nombre}");

        return redirect()->route('sgrh.cargo.index')->with('success', 'Cargo eliminado correctamente.');
    }

    private function validado(Request $request): array
    {
        return $request->validate([
            'nombre' => 'required|string|max:255',
            'sgrh_area_id' => 'nullable|exists:sgrh_areas,id',
            'jornada' => 'nullable|string|in:' . implode(',', self::JORNADAS),
            'jefe_inmediato_id' => 'nullable|exists:sgrh_cargos,id',
            'director_id' => 'nullable|exists:sgrh_cargos,id',
            // manual_funciones no se valida aquí: no hay campo de carga en el formulario
            // todavía. update() simplemente no lo toca (conserva el valor migrado de
            // gdo_cargo si lo tenía); en cargos nuevos queda null hasta que exista esa UI.
            'observaciones' => 'nullable|string',
        ]);
    }
}
