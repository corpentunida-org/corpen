<?php

namespace App\Http\Controllers\Seguros;

use App\Http\Controllers\Controller;
use App\Models\Seguros\SegBeneficios;
use Illuminate\Http\Request;
use App\Http\Controllers\AuditoriaController;

class SegBeneficiosController extends Controller
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
        $beneficio = SegBeneficios::create([
            'cedulaAsegurado' => $request->aseguradoId,
            'poliza' => $request->polizaId,
            'porcentajeDescuento' => $request->porbene,
            'valorDescuento' => $request->valorbene,
            'observaciones' => strtoupper($request->observacionesbene)
        ]);
        if ($beneficio) {
            $accion = "add beneficio a " . $request->aseguradoId . " por valor de " . $request->valorbene;
            $this->auditoria($accion);
            return redirect()->back()->with('success', 'Se agrego correctamente el beneficio.');
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(SegAsegurado $segAsegurado)
    {
        
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(SegAsegurado $segAsegurado)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, SegAsegurado $segAsegurado)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(SegAsegurado $segAsegurado)
    {
        //
    }
}
