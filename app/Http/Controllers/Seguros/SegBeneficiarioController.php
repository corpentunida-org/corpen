<?php

namespace App\Http\Controllers\Seguros;

use App\Http\Controllers\Controller;
use App\Models\Seguros\SegBeneficiario;
use App\Models\Seguros\Parentescos;
use Illuminate\Http\Request;

class SegBeneficiarioController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show($idAsegurado)
    {
        $beneficiarios = SegBeneficiario::where('id_asegurado', $idAsegurado)->get();
        return $beneficiarios;
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {            
        $beneficiario = SegBeneficiario::where('id', $id)->with(['asegurado.tercero'])->first();
        $parentescos = Parentescos::all();
        return view('seguros.beneficiarios.edit', compact('beneficiario', 'parentescos'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        //dd($request->aseguradoId);

        $beneficiario = SegBeneficiario::findOrFail($id);
        $beneficiario->update([
            'nombre' => strtoupper($request->input('names')),
            'parentesco' => $request->input('parentesco'),            
            'porcentaje' => $request->input('porcentaje'),            
            'telefono' => $request->input('telefono'),
            'correo' => $request->input('correo'),
        ]);
    
        $url = route('seguros.poliza.show', ['poliza' => 'ID']) . '?id=' . $request->aseguradoId;
        return redirect()->to($url)->with('success', 'Beneficiario actualizado correctamente');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(SegBeneficiario $segBeneficiario)
    {
        //
    }
}
