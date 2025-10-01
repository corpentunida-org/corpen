<?php

namespace App\Http\Controllers\Soportes;

use App\Http\Controllers\Controller;
use App\Models\Soportes\ScpCategoria;
use Illuminate\Http\Request;

class ScpCategoriaController extends Controller
{
    public function index()
    {
        return app(ScpTableroParametroController::class)->index();
    }

    public function create()
    {
        return view('soportes.categorias.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nombre' => 'required|string|max:255',
            'descripcion' => 'nullable|string',
        ]);

        ScpCategoria::create($request->all());

        return redirect()->route('soportes.categorias.index')
                         ->with('success', 'Categoría creada correctamente.');
    }

    public function edit(ScpCategoria $scpCategoria)
    {
        return view('soportes.categorias.edit', compact('scpCategoria'));
    }

    public function update(Request $request, ScpCategoria $scpCategoria)
    {
        $request->validate([
            'nombre' => 'required|string|max:255',
            'descripcion' => 'nullable|string',
        ]);

        $scpCategoria->update($request->all());

        return redirect()->route('soportes.categorias.index')
                         ->with('success', 'Categoría actualizada correctamente.');
    }

    public function destroy(ScpCategoria $scpCategoria)
    {
        $scpCategoria->delete();
        return redirect()->route('soportes.categorias.index')
                         ->with('success', 'Categoría eliminada correctamente.');
    }
}
