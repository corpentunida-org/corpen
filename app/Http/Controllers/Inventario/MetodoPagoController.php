<?php

namespace App\Http\Controllers\Inventario;
use App\Http\Controllers\Controller;
use App\Models\Inventario\InvMetodo;
use Illuminate\Http\Request;

class MetodoPagoController extends Controller
{
    public function index()
    {
        $metodos = InvMetodo::all();
        return view('inventario.catalogos.metodos.index', compact('metodos'));
    }
    
    public function store(Request $request)
    {
        InvMetodo::create($request->all());
        return back()->with('success', 'Método creado');
    }
}