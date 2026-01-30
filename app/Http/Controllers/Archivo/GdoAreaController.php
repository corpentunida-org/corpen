<?php

namespace App\Http\Controllers\Archivo;

use App\Http\Controllers\Controller;
use App\Models\Archivo\GdoArea;
use App\Models\Archivo\GdoCargo;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class GdoAreaController extends Controller
{
    /**
     * Lista las áreas con soporte de búsqueda y paginación optimizada.
     * Mejora: Se integra la lógica de búsqueda que requiere la vista UX.
     */
    public function index(Request $request): View
    {
        $query = GdoArea::with(['jefeCargo', 'cargos']);

        // Soporte para el input de búsqueda de la vista
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('nombre', 'LIKE', "%{$search}%")
                  ->orWhere('descripcion', 'LIKE', "%{$search}%");
            });
        }

        $areas = $query->latest('id')->paginate(20);

        return view('archivo.area.index', compact('areas'));
    }

    /**
     * Muestra el formulario de creación.
     */
    public function create(): View
    {
        // Traemos los cargos para asignar un jefe desde el inicio
        $cargos = GdoCargo::orderBy('nombre_cargo')->get();
        return view('archivo.area.create', compact('cargos'));
    }

    /**
     * Guarda una nueva área con validaciones profesionales.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'nombre'       => 'required|string|max:100|unique:gdo_area,nombre',
            'descripcion'  => 'nullable|string',
            'estado'       => 'required|in:activo,inactivo',
            'GDO_cargo_id' => 'nullable|exists:gdo_cargo,id',
        ]);

        GdoArea::create($validated);

        return redirect()->route('archivo.area.index')
                         ->with('success', 'El área organizacional se ha creado correctamente.');
    }

    /**
     * Muestra el detalle de una sola área.
     * Mejora: Carga anidada (Eager Loading) para optimizar el rendimiento.
     */
    public function show(GdoArea $area): View
    {
        $area->load(['jefeCargo', 'cargos.empleado']);
        return view('archivo.area.show', compact('area'));
    }

    /**
     * Muestra el formulario de edición.
     */
    public function edit(GdoArea $area): View
    {
        $cargos = GdoCargo::orderBy('nombre_cargo')->get();
        return view('archivo.area.edit', compact('area', 'cargos'));
    }

    /**
     * Actualiza un área con validación de nombre único exceptuando el actual.
     */
    public function update(Request $request, GdoArea $area): RedirectResponse
    {
        $validated = $request->validate([
            'nombre'       => "required|string|max:100|unique:gdo_area,nombre,{$area->id}",
            'descripcion'  => 'nullable|string',
            'estado'       => 'required|in:activo,inactivo',
            'GDO_cargo_id' => 'nullable|exists:gdo_cargo,id',
        ]);

        $area->update($validated);

        return redirect()->route('archivo.area.index')
                         ->with('success', 'Área actualizada correctamente.');
    }

    /**
     * Elimina un área con validación de integridad.
     * Mejora: Evita borrar áreas que aún tienen cargos asignados.
     */
    public function destroy(GdoArea $area): RedirectResponse
    {
        // Verificación de integridad referencial manual para UX
        if ($area->cargos()->exists()) {
            return back()->with('error', 'No se puede eliminar el área porque tiene cargos vinculados. Reasigne los cargos primero.');
        }

        $area->delete();

        return redirect()->route('archivo.area.index')
                         ->with('success', 'El área ha sido eliminada del sistema.');
    }
}