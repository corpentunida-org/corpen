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
        $request->validate(['nombre' => 'required|string|max:255']);
        InvMetodo::create($request->all());
        return back()->with('success', 'Método de pago creado');
    }
    
    public function update(Request $request, $id)
    {
        $request->validate(['nombre' => 'required|string|max:255']);
        
        $metodo = InvMetodo::findOrFail($id);
        $metodo->update($request->all());
        
        return back()->with('success', 'Método de pago actualizado');
    }
    
    public function destroy($id)
    {
        InvMetodo::destroy($id);
        return back()->with('success', 'Método de pago eliminado');
    }
}