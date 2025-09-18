<?php

namespace App\Http\Controllers\Soportes;

use App\Http\Controllers\Controller;
use App\Models\Soportes\ScpPrioridad;
use Illuminate\Http\Request;

class ScpPrioridadController extends Controller
{
    // Listar prioridades
    public function index()
    {
        return app(ScpTableroParametroController::class)->index();
    }
    // Formulario de creación
    public function create()
    {
        return view('soportes.prioridades.create');
    }
    // Guardar una nueva prioridad
    public function store(Request $request)
    {
        $request->validate([
            'nombre' => 'required|string|max:255',
        ]);

        ScpPrioridad::create($request->all());

        return redirect()->route('soportes.prioridades.index')
                         ->with('success', 'Prioridad creada correctamente.');
    }
    // Mostrar una prioridad específica
    public function show(ScpPrioridad $scpPrioridad)
    {
        return view('soportes.prioridades.show', compact('scpPrioridad'));
    }
    // Formulario de edición
    public function edit(ScpPrioridad $scpPrioridad)
    {
        return view('soportes.prioridades.edit', compact('scpPrioridad'));
    }
    // Actualizar prioridad
    public function update(Request $request, ScpPrioridad $scpPrioridad)
    {
        $request->validate([
            'nombre' => 'required|string|max:255',
        ]);

        $scpPrioridad->update($request->all());

        return redirect()->route('soportes.prioridades.index')
                         ->with('success', 'Prioridad actualizada correctamente.');
    }

    // Eliminar prioridad
    public function destroy(ScpPrioridad $scpPrioridad)
    {
        $scpPrioridad->delete();

        return redirect()->route('soportes.prioridades.index')
                         ->with('success', 'Prioridad eliminada correctamente.');
    }
}
