<?php

namespace App\Http\Controllers\Inventario;

use App\Http\Controllers\Controller;
use App\Models\Inventario\InvBodega;
use Illuminate\Http\Request;

class BodegaController extends Controller
{
    public function index()
    {
        $bodegas = InvBodega::all();
        return view('inventario.catalogos.bodegas.index', compact('bodegas'));
    }

    public function store(Request $request)
    {
        $request->validate(['nombre' => 'required']);
        $request->validate(['descripcion' => 'required']);
        InvBodega::create($request->all());
        return back()->with('success', 'Bodega creada');
    }

    public function update(Request $request, $id)
    {
        $bodega = InvBodega::findOrFail($id);
        $bodega->update($request->all());
        return back()->with('success', 'Bodega actualizada');
    }

    public function destroy($id)
    {
        InvBodega::destroy($id);
        return back()->with('success', 'Bodega eliminada');
    }
}