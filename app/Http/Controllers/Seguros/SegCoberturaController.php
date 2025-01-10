<?php

namespace App\Http\Controllers\Seguros;

use App\Http\Controllers\Controller;
use App\Models\Seguros\SegCobertura;
use Illuminate\Http\Request;
use App\Http\Controllers\AuditoriaController;

class SegCoberturaController extends Controller
{
    /**
     * Display a listing of the resource.
     */

     private function auditoria($accion){
        $auditoriaController = app(AuditoriaController::class);
        $auditoriaController->create($accion, "SEGUROS");
    }

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
        $cobertura = SegCobertura::create([
            'nombre' => strtoupper($request->input('cobname')),
            'convenio_id' => $request->input('cobconvenio'),
            'observacion' => $request->input('cobOservacion'),
        ]);
        if (!$cobertura->exists) {
            return redirect()->back()->with('error', 'No se pudo procesar la cobertura.');
        }

        $this->auditoria("COBERTURA CREADA ID " . $cobertura->id);
        return redirect()->route('seguros.planes.index')->with('success', 'La cobertura fue creada exitosamente.');
    }

    /**
     * Display the specified resource.
     */
    public function show($convenio_id)
    {
        $coberturas = SegCobertura::where('convenio_id', $convenio_id)->get();
        return response()->json($coberturas);
    }
    

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(SegCobertura $segCobertura)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, SegCobertura $segCobertura)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(SegCobertura $segCobertura)
    {
        //
    }
}
