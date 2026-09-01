<?php

namespace App\Http\Controllers\Sgrh;

use App\Http\Controllers\AuditoriaController;
use App\Http\Controllers\Controller;
use App\Models\Sgrh\Area;
use App\Models\Sgrh\Cargo;
use Illuminate\Http\Request;

class AreaController extends Controller
{
    private function auditoria($accion)
    {
        app(AuditoriaController::class)->create($accion, 'SGRH');
    }

    public function index(Request $request)
    {
        // Eager load: sin esto, cada fila listada dispararía su propia consulta a
        // sgrh_cargos por el responsable (N+1) — la BD es remota, cuesta caro.
        $query = Area::with('cargoResponsable')->withCount('cargos');

        if ($request->filled('search')) {
            $query->where('nombre', 'like', "%{$request->search}%");
        }

        $areas = $query->orderBy('nombre')->paginate(20)->appends($request->query());

        return view('sgrh.area.index', compact('areas'));
    }

    public function create()
    {
        $cargos = Cargo::where('activo', true)->orderBy('nombre')->get();

        return view('sgrh.area.create', compact('cargos'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nombre' => 'required|string|max:255|unique:sgrh_areas,nombre',
            'descripcion' => 'nullable|string',
            'cargo_responsable_id' => 'nullable|exists:sgrh_cargos,id',
            'activo' => 'nullable|boolean',
        ]);

        $validated['activo'] = $request->boolean('activo', true);

        $area = Area::create($validated);

        $this->auditoria("Área creada: {$area->nombre} (id {$area->id})");

        return redirect()->route('sgrh.area.index')->with('success', 'Área creada correctamente.');
    }

    public function edit(Area $area)
    {
        $cargos = Cargo::where('activo', true)->orderBy('nombre')->get();

        return view('sgrh.area.edit', compact('area', 'cargos'));
    }

    public function update(Request $request, Area $area)
    {
        $validated = $request->validate([
            'nombre' => 'required|string|max:255|unique:sgrh_areas,nombre,' . $area->id,
            'descripcion' => 'nullable|string',
            'cargo_responsable_id' => 'nullable|exists:sgrh_cargos,id',
            'activo' => 'nullable|boolean',
        ]);

        $validated['activo'] = $request->boolean('activo', true);

        $area->update($validated);

        $this->auditoria("Área actualizada: {$area->nombre} (id {$area->id})");

        return redirect()->route('sgrh.area.index')->with('success', 'Área actualizada correctamente.');
    }

    public function destroy(Area $area)
    {
        // Guardia de aplicación, no de BD: sgrh_cargos.sgrh_area_id es nullOnDelete, así que
        // sin este chequeo el área se borraría igual y solo dejaría sus cargos huérfanos.
        if ($area->cargos()->exists()) {
            return back()->with('error', 'No se puede eliminar el área porque tiene cargos vinculados. Reasigna o elimina esos cargos primero.');
        }

        $nombre = $area->nombre;
        $area->delete();

        $this->auditoria("Área eliminada: {$nombre}");

        return redirect()->route('sgrh.area.index')->with('success', 'Área eliminada correctamente.');
    }
}
