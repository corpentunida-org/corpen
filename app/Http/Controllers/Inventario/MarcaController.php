<?php

namespace App\Http\Controllers\Inventario;

use App\Http\Controllers\Controller;
use App\Models\Inventario\InvMarca;
use Illuminate\Http\Request;

class MarcaController extends Controller
{
    public function index(Request $request)
    {
        $query = InvMarca::query();

        // El buscador ahora también filtra por 'modelo'
        if ($request->filled('q')) {
            $q = $request->q;
            $query->where('nombre', 'LIKE', "%{$q}%")
                  ->orWhere('descripcion', 'LIKE', "%{$q}%")
                  ->orWhere('modelo', 'LIKE', "%{$q}%");
        }

        $marcas = $query->orderBy('id', 'desc')->paginate(10);

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'items' => $marcas->items()
            ]);
        }

        return view('inventario.catalogos.marcas.index', compact('marcas'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nombre' => 'required|string|max:255|unique:inv_marcas,nombre',
            'modelo' => 'nullable|string|max:255', // <-- Nueva validación
            'descripcion' => 'nullable|string'
        ], [
            'nombre.required' => 'El nombre de la marca es obligatorio.',
            'nombre.unique' => 'Ya existe una marca con este nombre.'
        ]);

        InvMarca::create($request->all());
        
        return back()->with('success', 'Marca y modelo agregados exitosamente.');
    }
    
    public function update(Request $request, $id)
    {
        $request->validate([
            'nombre' => 'required|string|max:255|unique:inv_marcas,nombre,' . $id,
            'modelo' => 'nullable|string|max:255', // <-- Nueva validación
            'descripcion' => 'nullable|string'
        ], [
            'nombre.required' => 'El nombre es obligatorio.',
            'nombre.unique' => 'Este nombre ya está en uso.'
        ]);
        
        $marca = InvMarca::findOrFail($id);
        $marca->update($request->all());
        
        return back()->with('success', 'Datos actualizados correctamente.');
    }
}