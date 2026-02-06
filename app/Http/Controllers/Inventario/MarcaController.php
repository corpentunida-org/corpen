<?php

namespace App\Http\Controllers\Inventario;
use App\Http\Controllers\Controller;
use App\Models\Inventario\InvMarca;
use Illuminate\Http\Request;

class MarcaController extends Controller
{
    public function index()
    {
        $marcas = InvMarca::all();
        return view('inventario.catalogos.marcas.index', compact('marcas'));
    }

    public function store(Request $request)
    {
        InvMarca::create($request->all());
        return back()->with('success', 'Marca creada');
    }
    
    public function update(Request $request, $id)
    {
        $request->validate([
            'nombre' => 'required|string|max:255',
            'descripcion' => 'nullable|string'
        ]);
        
        $marca = InvMarca::findOrFail($id);
        $marca->update($request->all());
        
        return back()->with('success', 'Marca actualizada');
    }
}