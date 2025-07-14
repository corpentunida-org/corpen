<?php

namespace App\Http\Controllers\Maestras;

use App\Http\Controllers\Controller;
use App\Models\Maestras\claseCongregacion;
use App\Models\Maestras\Congregacion;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class CongregacionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
       $congregacion = Congregacion::with('claseCongregacion')->get();

 // debería ser un objeto ClaseCongregacion

        return view('maestras.congregaciones.index', compact('congregacion'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $claselist = claseCongregacion::all();
        return view('maestras.congregaciones.create',compact('claselist'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'codigo' => 'required|string|max:255|unique:Congregaciones,codigo',
            'nombre' => 'required|string|max:255',
            'estado' => 'required|in:1,0', // asumes tinyint: 1 = Activo, 0 = Inactivo
            'clase' => 'nullable|integer',
            'municipio' => 'nullable|string|max:255',
            'direccion' => 'nullable|string|max:255',
            'telefono' => 'nullable|string|max:45',
            'celular' => 'nullable|string|max:45',
            'distrito' => 'nullable|string|max:10',
            'apertura' => 'nullable|string|max:45',
            'cierre' => 'nullable|string|max:45',
            'observacion' => 'nullable|string|max:255',
            'pastor' => 'nullable|integer',
        ]);

        Congregacion::create($request->all());

        return redirect()->route('maestras.congregacion.index')
                         ->with('success', '¡Congregación creada exitosamente!');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($congregacionid)
    {
        $congregacion = Congregacion::where('codigo', $congregacionid)->first();
        return view('maestras.congregaciones.edit', compact('congregacion'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Congregacion $congregacione)
    {
        $request->validate([
            'codigo' => [
                'required',
                'string',
                'max:255',
                Rule::unique('Congregaciones', 'codigo')->ignore($congregacione->id),
            ],
            'nombre' => 'required|string|max:255',
            'estado' => 'required|in:1,0',
            'clase' => 'nullable|integer',
            'municipio' => 'nullable|string|max:255',
            'direccion' => 'nullable|string|max:255',
            'telefono' => 'nullable|string|max:45',
            'celular' => 'nullable|string|max:45',
            'distrito' => 'nullable|string|max:10',
            'apertura' => 'nullable|string|max:45',
            'cierre' => 'nullable|string|max:45',
            'observacion' => 'nullable|string|max:255',
            'pastor' => 'nullable|integer',
        ]);

        $congregacione->update($request->all());

        return redirect()->route('maestras.congregacion.index')
                         ->with('success', '¡Congregación actualizada exitosamente!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
