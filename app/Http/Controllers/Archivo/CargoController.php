<?php

namespace App\Http\Controllers\Archivo;

use App\Http\Controllers\Controller;
use App\Models\Archivo\Cargo;
use Illuminate\Http\Request;

class CargoController extends Controller
{
    public function index()
    {
        /* $cargos = Cargo::all(); */
        $cargos = Cargo::paginate(10); // o el número de ítems por página

        return view('archivo.cargo.index', compact('cargos'));
    }

    public function create()
    {
        return view('archivo.cargo.create');
    }

    public function store(Request $request)
    {

        $request->validate([
            'nombre_cargo'         => 'nullable|string|max:255',
            'salario_base'         => 'nullable|numeric|min:0',
            'jornada'              => 'nullable|string|max:100',
            'telefono_corporativo' => 'nullable|string|max:50',
            'celular_corporativo'  => 'nullable|string|max:50',
            'ext_corporativo'      => 'nullable|string|max:20',
            'correo_corporativo'   => 'nullable|email|max:255',
            'gmail_corporativo'    => 'nullable|email|max:255',
            'manual_funciones'     => 'nullable|string|max:255',
            'empleado_cedula'      => 'nullable|string|max:50',
            'estado'               => 'nullable|boolean',
            'observacion'          => 'nullable|string',
        ]);
        Cargo::create($request->all());
        return redirect()->route('archivo.cargo.index')->with('success', 'Cargo creado correctamente.');
    }

    public function show(Cargo $cargo)
    {
        return view('archivo.cargo.show', compact('cargo'));
    }

    public function edit(Cargo $cargo)
    {
        return view('archivo.cargo.edit', compact('cargo'));
    }

    public function update(Request $request, Cargo $cargo)
    {
        $request->validate([
            'nombre_cargo' => 'required|string|max:255',
            'salario_base' => 'nullable|numeric',
            'jornada' => 'nullable|string|max:100',
            'telefono_corporativo' => 'nullable|string|max:50',
            'celular_corporativo' => 'nullable|string|max:50',
            'ext_corporativo' => 'nullable|string|max:20',
            'correo_corporativo' => 'nullable|email|max:255',
            'gmail_corporativo' => 'nullable|email|max:255',
            'manual_funciones' => 'nullable|string|max:255',
            'empleado_cedula' => 'nullable|string|max:50',
            'estado' => 'nullable|boolean',
            'observacion' => 'nullable|string',
        ]);

        $cargo->update($request->all());

        return redirect()->route('archivo.cargos.index')->with('success', 'Cargo actualizado.');
    }

    public function destroy(Cargo $cargo)
    {
        $cargo->delete();
        return redirect()->route('archivo.cargos.index')->with('success', 'Cargo eliminado.');
    }
}
