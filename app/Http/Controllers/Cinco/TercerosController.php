<?php

namespace App\Http\Controllers\Cinco;

use App\Http\Controllers\Controller;
use App\Models\Cinco\Terceros;
use Illuminate\Http\Request;
use App\Http\Controllers\AuditoriaController;

class TercerosController extends Controller
{
    /**
     * Display a listing of the resource.
     */

    private function auditoria($accion){
        $auditoriaController = app(AuditoriaController::class);
        $auditoriaController->create($accion, "CINCO");
    }

    public function index()
    {
        $terceros = Terceros::all();
        return view('cinco.terceros.index', compact('terceros'));
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
    public function show(Request $request)
    {
        $id = $request->input('id');
        $tercero = Terceros::where('Cod_Ter', $id)->first();
        if (!$tercero) {
            return redirect()->route('cinco.tercero.index')->with('warning', 'No existe esa cédula en la lista de terceros cinco');
        }
        return view('cinco.terceros.show', compact('tercero'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Terceros $terceros)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {        
        $ter = Terceros::where('Cod_Ter', $id)->first();
        
        $ter->update([
            'Fec_Ing' => $request->input('Fec_Ing'),            
            'Fec_Minis' => $request->input('Fec_Minis'),            
            'Fec_Aport' => $request->input('Fec_Aport'),
            'observacion'=> $request->input('observacion'),
            'verificado'=> true,
            'verificadousuario'=> auth()->user()->name
        ]);
        $accion = "Actualizar fechas terceros " . $id;
        $this->auditoria($accion);
        return redirect()->back()->with('success', 'Registro actualizado correctamente.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Terceros $terceros)
    {
        //
    }
}
