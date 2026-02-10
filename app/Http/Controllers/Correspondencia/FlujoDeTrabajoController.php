<?php

namespace App\Http\Controllers\Correspondencia;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Correspondencia\FlujoDeTrabajo;
use App\Models\User;

class FlujoDeTrabajoController extends Controller
{
    public function index()
    {
        $flujos = FlujoDeTrabajo::with('usuario')->paginate(15);
        return view('correspondencia.flujos.index', compact('flujos'));
    }

    public function create()
    {
        $usuarios = User::all();
        return view('correspondencia.flujos.create', compact('usuarios'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'nombre' => 'required|string|max:150',
            'detalle' => 'nullable|string',
            'usuario_id' => 'required|exists:users,id',
        ]);

        FlujoDeTrabajo::create($data);

        return redirect()->route('correspondencia.flujos.index')->with('success','Flujo creado correctamente');
    }

    public function show(FlujoDeTrabajo $flujo)
    {
        return view('correspondencia.flujos.show', compact('flujo'));
    }

    public function edit(FlujoDeTrabajo $flujo)
    {
        $usuarios = User::all();
        return view('correspondencia.flujos.edit', compact('flujo','usuarios'));
    }

    public function update(Request $request, FlujoDeTrabajo $flujo)
    {
        $data = $request->validate([
            'nombre' => 'required|string|max:150',
            'detalle' => 'nullable|string',
            'usuario_id' => 'required|exists:users,id',
        ]);

        $flujo->update($data);

        return redirect()->route('correspondencia.flujos.index')->with('success','Flujo actualizado correctamente');
    }

    public function destroy(FlujoDeTrabajo $flujo)
    {
        $flujo->delete();
        return redirect()->route('correspondencia.flujos.index')->with('success','Flujo eliminado correctamente');
    }
}
