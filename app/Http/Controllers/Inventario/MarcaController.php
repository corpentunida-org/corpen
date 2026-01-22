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
}