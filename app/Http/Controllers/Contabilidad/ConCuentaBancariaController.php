<?php

namespace App\Http\Controllers\Contabilidad;

use App\Http\Controllers\Controller;
use App\Models\Contabilidad\ConCuentaBancaria;
use Illuminate\Http\Request;

class ConCuentaBancariaController extends Controller
{
    public function index()
    {
        // Traemos los datos de la DB
        $cuentas = ConCuentaBancaria::all();
        
        // Retornamos la VISTA pasándole la variable $cuentas
        return view('contabilidad.cuentas.index', compact('cuentas'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'banco'         => 'required|string',
            'numero_cuenta' => 'required|string|max:200|unique:con_cuentas_bancarias,numero_cuenta,' . ($conCuentaBancaria->id ?? ''),
            'tipo_cuenta'   => 'required|string|max:45',
            'num_siasoft'   => 'required|integer',
            'estado'        => 'required|string|max:45', // Campo nuevo agregado
            'convenios'     => 'nullable|string|max:45',
            'id_user'       => 'required|integer',
        ]);

        ConCuentaBancaria::create($validated);

        // Redireccionamos a la lista con un mensaje de éxito
        return redirect()->route('contabilidad.cuentas-bancarias.index')
                         ->with('success', '¡Cuenta bancaria creada con éxito!');
    }

    public function update(Request $request, ConCuentaBancaria $conCuentaBancaria)
    {
        $validated = $request->validate([
            'banco'         => 'sometimes|string',
            'numero_cuenta' => 'required|string|max:200|unique:con_cuentas_bancarias,numero_cuenta,' . ($conCuentaBancaria->id ?? ''),
            'tipo_cuenta'   => 'sometimes|string|max:45',
            'num_siasoft'   => 'sometimes|integer',
            'estado'        => 'sometimes|string|max:45', // Campo nuevo agregado
            'convenios'     => 'nullable|string|max:45',
            'id_user'       => 'sometimes|integer',
        ]);

        $conCuentaBancaria->update($validated);

        return redirect()->route('contabilidad.cuentas-bancarias.index')
                         ->with('success', 'Cuenta actualizada correctamente.');
    }

    public function destroy(ConCuentaBancaria $conCuentaBancaria)
    {
        $conCuentaBancaria->delete();

        return redirect()->route('contabilidad.cuentas-bancarias.index')
                         ->with('success', 'Cuenta eliminada.');
    }
}