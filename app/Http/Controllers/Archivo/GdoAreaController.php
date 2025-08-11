<?php

namespace App\Http\Controllers\Archivo;

use App\Http\Controllers\Controller;
use App\Models\Archivo\GdoArea;
use App\Models\Archivo\GdoCargo;
use Illuminate\Http\Request;

class GdoAreaController extends Controller
{
    /**
     * Lista las áreas con su jefe y cargos asociados.
     */
    public function index()
    {
        $areas = GdoArea::with(['jefeCargo', 'cargos'])
                        ->orderBy('id', 'desc')
                        ->paginate(20);

        return view('archivo.area.index', compact('areas'));
    }

    /**
     * Muestra el formulario de creación.
     */
    public function create()
    {
        $cargos = GdoCargo::orderBy('nombre_cargo')->get(); // Para seleccionar el jefe del área
        return view('archivo.area.create', compact('cargos'));
    }

    /**
     * Guarda una nueva área.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nombre'       => 'required|string|max:100',
            'descripcion'  => 'nullable|string',
            'estado'       => 'required|in:activo,inactivo',
            'GDO_cargo_id' => 'nullable|exists:gdo_cargo,id', // Jefe del área
        ]);

        GdoArea::create($validated);

        return redirect()->route('archivo.area.index')
                         ->with('success', 'Área creada correctamente');
    }

    /**
     * Muestra una sola área con relaciones.
     */
    public function show(GdoArea $area)
    {
        $area->load(['jefeCargo', 'cargos']);
        return view('archivo.area.show', compact('area'));
    }

    /**
     * Muestra el formulario de edición.
     */
    public function edit(GdoArea $area)
    {
        $cargos = GdoCargo::orderBy('nombre_cargo')->get(); // Para poder cambiar el jefe del área
        return view('archivo.area.edit', compact('area', 'cargos'));
    }

    /**
     * Actualiza un área.
     */
    public function update(Request $request, GdoArea $area)
    {
        $validated = $request->validate([
            'nombre'       => 'required|string|max:100',
            'descripcion'  => 'nullable|string',
            'estado'       => 'required|in:activo,inactivo',
            'GDO_cargo_id' => 'nullable|exists:gdo_cargo,id',
        ]);

        $area->update($validated);

        return redirect()->route('archivo.area.index')
                         ->with('success', 'Área actualizada correctamente');
    }

    /**
     * Elimina un área.
     */
    public function destroy(GdoArea $area)
    {
        $area->delete();

        return redirect()->route('archivo.area.index')
                         ->with('success', 'Área eliminada correctamente');
    }
}
