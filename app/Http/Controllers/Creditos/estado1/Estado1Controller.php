<?php

namespace App\Http\Controllers\Creditos\Estado1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Creditos\CreFabrica;

class Estado1Controller extends Controller
{
    public function index()
    {
        $fabricas = CreFabrica::paginate(15);
        return view('creditos.estado1.index', compact('fabricas'));
    }

    public function create()
    {
        return view('creditos.estado1.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nombre' => 'required|string|max:255',
            'cuenta' => 'nullable|numeric',
            'tipo' => 'nullable|string',
            'acuerdo' => 'nullable|string',
            'tasa_interes' => 'nullable|numeric',
            'plazo_maximo' => 'nullable|integer',
            'plazo_minimo' => 'nullable|integer',
            'edad_minima' => 'nullable|integer',
            'edad_maxima' => 'nullable|integer',
            'fecha_apertura' => 'nullable|date',
            'fecha_cierre' => 'nullable|date',
            'observacion' => 'nullable|string',
            'id_garantia' => 'nullable|array',
        ]);

        CreFabrica::create($request->all());

        return redirect()->route('estado1.index')->with('success', 'Fábrica creada correctamente.');
    }

    public function edit(CreFabrica $fabrica)
    {
        return view('creditos.estado1.edit', compact('fabrica'));
    }

    public function update(Request $request, CreFabrica $fabrica)
    {
        $request->validate([
            'nombre' => 'required|string|max:255',
            'cuenta' => 'nullable|numeric',
            'tipo' => 'nullable|string',
            'acuerdo' => 'nullable|string',
            'tasa_interes' => 'nullable|numeric',
            'plazo_maximo' => 'nullable|integer',
            'plazo_minimo' => 'nullable|integer',
            'edad_minima' => 'nullable|integer',
            'edad_maxima' => 'nullable|integer',
            'fecha_apertura' => 'nullable|date',
            'fecha_cierre' => 'nullable|date',
            'observacion' => 'nullable|string',
            'id_garantia' => 'nullable|array',
        ]);

        $fabrica->update($request->all());

        return redirect()->route('estado1.index')->with('success', 'Fábrica actualizada correctamente.');
    }

    public function destroy(CreFabrica $fabrica)
    {
        $fabrica->delete();
        return redirect()->route('estado1.index')->with('success', 'Fábrica eliminada correctamente.');
    }

    public function mostrarFormulario()
    {
        $tiposCredito = CreFabrica::select('id', 'nombre')->get();
        return view('creditos.estado1.formulario', compact('tiposCredito'));
    }
}
