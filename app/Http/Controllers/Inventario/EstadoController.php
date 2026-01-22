<?php

namespace App\Http\Controllers\Inventario;

use App\Http\Controllers\Controller;
use App\Models\Inventario\InvEstado;
use Illuminate\Http\Request;

class EstadoController extends Controller
{
    public function index()
    {
        $estados = InvEstado::all();
        return view('inventario.catalogos.estados.index', compact('estados'));
    }

    public function store(Request $request)
    {
        InvEstado::create($request->all());
        return back()->with('success', 'Estado creado');
    }
    
    // Update y Destroy son iguales a los de Bodega...
}