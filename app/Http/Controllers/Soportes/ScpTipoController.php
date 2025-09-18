<?php

namespace App\Http\Controllers\Soportes;

use App\Http\Controllers\Controller;
use App\Models\Soportes\ScpTipo;
use Illuminate\Http\Request;

class ScpTipoController extends Controller
{
    // Listar tipos (usa el tablero)
    public function index()
    {
        return app(ScpTableroParametroController::class)->index();
    }

    // Formulario de creación
    public function create()
    {
        return view('soportes.tipos.create');
    }

    // Guardar un nuevo tipo
    public function store(Request $request)
    {
        $request->validate([
            'nombre' => 'required|string|max:255',
        ]);

        ScpTipo::create($request->all());

        return redirect()->route('soportes.tipos.index')
                         ->with('success', 'Tipo creado correctamente.');
    }

    // Mostrar un tipo específico
    public function show(ScpTipo $scpTipo)
    {
        return view('soportes.tipos.show', compact('scpTipo'));
    }

    // Formulario de edición
    public function edit(ScpTipo $scpTipo)
    {
        return view('soportes.tipos.edit', compact('scpTipo'));
    }

    // Actualizar un tipo
    public function update(Request $request, ScpTipo $scpTipo)
    {
        $request->validate([
            'nombre' => 'required|string|max:255',
        ]);

        $scpTipo->update($request->all());

        return redirect()->route('soportes.tipos.index')
                         ->with('success', 'Tipo actualizado correctamente.');
    }

    // Eliminar un tipo
    public function destroy(ScpTipo $scpTipo)
    {
        $scpTipo->delete();

        return redirect()->route('soportes.tipos.index')
                         ->with('success', 'Tipo eliminado correctamente.');
    }
}
