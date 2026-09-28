<?php

namespace App\Http\Controllers\Soportes;

use App\Http\Controllers\Controller;
use App\Models\Soportes\ScpEstado;
use Illuminate\Http\Request;

class ScpEstadoController extends Controller
{
    // Mostrar listado de estados
    public function index()
    {
        return app(ScpTableroParametroController::class)->index();
    }

    // Formulario de creación
    public function create()
    {
        return view('soportes.estados.create');
    }

    // Guardar un nuevo estado
    public function store(Request $request)
    {
        $request->validate([
            'nombre' => 'required|string|max:255',
            'descripcion' => 'nullable|string',
            'color' => 'nullable|string|max:20',
        ]);

        ScpEstado::create($request->all());

        return redirect()->route('soportes.estados.index')
                         ->with('success', 'Estado creado correctamente.');
    }

    // Mostrar un estado específico
    public function show(ScpEstado $scpEstado)
    {
        return view('soportes.estados.show', compact('scpEstado'));
    }

    // Formulario de edición
    public function edit(ScpEstado $scpEstado)
    {
        return view('soportes.estados.edit', compact('scpEstado'));
    }

    // Actualizar un estado
    public function update(Request $request, ScpEstado $scpEstado)
    {
        $request->validate([
            'nombre' => 'required|string|max:255',
            'descripcion' => 'nullable|string',
            'color' => 'nullable|string|max:20',
        ]);

        // ScpEstado::idPorNombre() cachea por nombre — si el nombre cambia, hay que olvidar
        // también el nombre VIEJO (no solo llamar olvidarCache(), que solo recorre los actuales).
        \Illuminate\Support\Facades\Cache::forget('scp_estado_id_'.strtolower($scpEstado->nombre));

        $scpEstado->update($request->all());
        ScpEstado::olvidarCache();

        return redirect()->route('soportes.estados.index')
                         ->with('success', 'Estado actualizado correctamente.');
    }

    // Eliminar un estado
    public function destroy(ScpEstado $scpEstado)
    {
        \Illuminate\Support\Facades\Cache::forget('scp_estado_id_'.strtolower($scpEstado->nombre));
        $scpEstado->delete();

        return redirect()->route('soportes.estados.index')
                         ->with('success', 'Estado eliminado correctamente.');
    }
}
