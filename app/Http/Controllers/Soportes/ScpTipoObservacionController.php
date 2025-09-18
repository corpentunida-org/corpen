<?php

namespace App\Http\Controllers\Soportes;

use App\Http\Controllers\Controller;
use App\Models\Soportes\ScpTipoObservacion;
use Illuminate\Http\Request;

class ScpTipoObservacionController extends Controller
{
    // Listar tipos de observación (usa el tablero)
    public function index()
    {
        return app(ScpTableroParametroController::class)->index();
    }

    // Formulario de creación
    public function create()
    {
        return view('soportes.tipo_observaciones.create');
    }

    // Guardar un nuevo tipo de observación
    public function store(Request $request)
    {
        $request->validate([
            'nombre' => 'required|string|max:255',
        ]);

        ScpTipoObservacion::create($request->all());

        return redirect()->route('soportes.tipoObservaciones.index')
                         ->with('success', 'Tipo de observación creado correctamente.');
    }

    // Mostrar un tipo de observación específico
    public function show(ScpTipoObservacion $scpTipoObservacion)
    {
        return view('soportes.tipo_observaciones.show', compact('scpTipoObservacion'));
    }

    // Formulario de edición
    public function edit(ScpTipoObservacion $scpTipoObservacion)
    {
        return view('soportes.tipo_observaciones.edit', compact('scpTipoObservacion'));
    }

    // Actualizar un tipo de observación
    public function update(Request $request, ScpTipoObservacion $scpTipoObservacion)
    {
        $request->validate([
            'nombre' => 'required|string|max:255',
        ]);

        $scpTipoObservacion->update($request->all());

        return redirect()->route('soportes.tipoObservaciones.index')
                         ->with('success', 'Tipo de observación actualizado correctamente.');
    }

    // Eliminar un tipo de observación
    public function destroy(ScpTipoObservacion $scpTipoObservacion)
    {
        $scpTipoObservacion->delete();

        return redirect()->route('soportes.tipoObservaciones.index')
                         ->with('success', 'Tipo de observación eliminado correctamente.');
    }
}
