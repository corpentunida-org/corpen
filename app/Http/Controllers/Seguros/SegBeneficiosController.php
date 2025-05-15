<?php

namespace App\Http\Controllers\Seguros;

use App\Http\Controllers\Controller;
use App\Models\Seguros\SegBeneficios;
use App\Models\Seguros\SegPoliza;
use App\Models\Seguros\SegPlan;
use Illuminate\Http\Request;
use App\Http\Controllers\AuditoriaController;
use Carbon\Carbon;

class SegBeneficiosController extends Controller
{
    /**
     * Display a listing of the resource.
     */

    private function auditoria($accion)
    {
        $auditoriaController = app(AuditoriaController::class);
        $auditoriaController->create($accion, "SEGUROS");
    }

    public function index()
    {
        $planes = SegPlan::where('vigente', true)->where('condicion_id', 2)->get();
        return view('seguros.beneficios.index', compact('planes'));
    }

    public function listFilter(Request $request)
    {
        $fechaMax = Carbon::today()->copy()->subYears($request->edad_minima);
        $fechaMin = Carbon::today()->copy()->subYears($request->edad_maxima + 1)->addDay();
        $listadata = SegPoliza::whereHas('tercero', function ($query) use ($fechaMin, $fechaMax) {
            $query->whereBetween('fechaNacimiento', [$fechaMin, $fechaMax]);
        })->where('active', true)->with(['plan', 'tercero', 'asegurado'])->get();
        $planes = SegPlan::where('vigente', true)->where('condicion_id', 2)->get();
        return view('seguros.beneficios.index', compact('listadata', 'planes'));
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
    public function show(SegBeneficios $SegBeneficios)
    {

    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(SegBeneficios $SegBeneficios)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, SegBeneficios $SegBeneficios)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(SegBeneficios $SegBeneficios)
    {
        //
    }
}
