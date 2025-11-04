<?php

namespace App\Http\Controllers\Soportes;

use App\Http\Controllers\Controller;
use App\Models\Soportes\ScpTipo;
use App\Models\Soportes\ScpCategoria;
use Illuminate\Http\Request;

class ScpTipoController extends Controller
{
    public function index()
    {
        return app(ScpTableroParametroController::class)->index();
    }

    public function create()
    {
        $categorias = ScpCategoria::pluck('nombre', 'id');
        return view('soportes.tipos.create', compact('categorias'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nombre' => 'required|string|max:255|unique:scp_tipos,nombre',
            'descripcion' => 'nullable|string',
            'id_categoria' => 'required|exists:scp_categorias,id',
        ]);

        ScpTipo::create($request->all());

        return redirect()->route('soportes.tablero')
                         ->with('success', 'Tipo de soporte creado correctamente.');
    }

    public function show(ScpTipo $scpTipo)
    {
        return view('soportes.tipos.show', compact('scpTipo'));
    }

    public function edit(ScpTipo $scpTipo)
    {
        $categorias = ScpCategoria::pluck('nombre', 'id');
        return view('soportes.tipos.edit', compact('scpTipo', 'categorias'));
    }

    public function update(Request $request, ScpTipo $scpTipo)
    {
        $request->validate([
            'nombre' => 'required|string|max:255|unique:scp_tipos,nombre,' . $scpTipo->id,
            'descripcion' => 'nullable|string',
            'id_categoria' => 'required|exists:scp_categorias,id',
        ]);

        $scpTipo->update($request->all());

        return redirect()->route('soportes.tablero')
                         ->with('success', 'Tipo de soporte actualizado correctamente.');
    }

    public function destroy(ScpTipo $scpTipo)
    {
        $scpTipo->delete();

        return redirect()->route('soportes.tablero')
                         ->with('success', 'Tipo de soporte eliminado correctamente.');
    }
}
