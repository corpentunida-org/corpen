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
        $query = Cargo::with('area')->withCount('empleados');

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

        return view('sgrh.cargo.create', compact('areas', 'jornadas'));
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

        return view('sgrh.cargo.edit', compact('cargo', 'areas', 'jornadas'));
    }

    public function update(Request $request, Cargo $cargo)
    {
        $validated = $this->validado($request);
        $validated['activo'] = $request->boolean('activo', true);

        $cargo->update($validated);

        $this->auditoria("Cargo actualizado: {$cargo->nombre} (id {$cargo->id})");

        return redirect()->route('sgrh.cargo.index')->with('success', 'Cargo actualizado correctamente.');
    }

    public function destroy(Cargo $cargo)
    {
        // Guardia de aplicación, no de BD: sgrh_empleados.cargo_id es nullOnDelete, así que
        // sin este chequeo el cargo se borraría igual y solo dejaría empleados sin cargo.
        if ($cargo->empleados()->exists()) {
            return back()->with('error', 'No se puede eliminar el cargo porque tiene colaboradores asignados. Reasígnalos primero.');
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
            'salario_base' => 'nullable|numeric|min:0',
            'jornada' => 'nullable|string|in:' . implode(',', self::JORNADAS),
            'telefono_corporativo' => 'nullable|string|max:50',
            'celular_corporativo' => 'nullable|string|max:50',
            'ext_corporativo' => 'nullable|string|max:20',
            'correo_corporativo' => 'nullable|email|max:255',
            'gmail_corporativo' => 'nullable|email|max:255',
            // manual_funciones no se valida aquí: no hay campo de carga en el formulario
            // todavía. update() simplemente no lo toca (conserva el valor migrado de
            // gdo_cargo si lo tenía); en cargos nuevos queda null hasta que exista esa UI.
            'observaciones' => 'nullable|string',
        ]);
    }
}
