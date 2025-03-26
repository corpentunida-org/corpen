<?php

namespace App\Http\Controllers\Seguros;

use App\Http\Controllers\Controller;
use App\Models\Seguros\SegPlan;
use App\Models\Seguros\SegCobertura;
use App\Models\Seguros\SegConvenio;
use App\Models\Seguros\SegCondicion;
use App\Models\Seguros\SegPlan_cobertura;
use Illuminate\Http\Request;
use App\Http\Controllers\AuditoriaController;

class SegPlanController extends Controller
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
        $planes = SegPlan::with(['condicion', 'convenio'])
            ->get()
            ->groupBy('condicion');
        return view('seguros.planes.index', compact('planes'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $coberturas = SegCobertura::all();
        $convenios = SegConvenio::all();
        $condiciones = SegCondicion::all();
        return view('seguros.planes.create', compact('coberturas', 'convenios', 'condiciones'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $plan = SegPlan::create([
            'name' => strtoupper($request->input('name')),
            'valor' => $request->input('valorPlanAsegurado'),
            'prima' => $request->input('prima'),
            'seg_convenio_id' => $request->input('convenio'),
            'condicion_id' => $request->input('condicion_id'),
        ]);
        if (!$plan->exists) {
            return redirect()->back()->with('error', 'No se pudo procesar el plan.');
        }
        
        $coberturaIds = $request->input('cobertura_id'); 
        $valoresAse = $request->input('valorAsegurado');
        $valoresCob = $request->input('valorPrima');

        for ($i = 0; $i < count($coberturaIds); $i++) {
            $plan->coberturas()->attach(
                $coberturaIds[$i], [
                    'valorAsegurado' => $valoresAse[$i],
                    'valorCobertura' => $valoresCob[$i],
                ]
            );
        }
        $idConvenio = $request->input('idConveniobusqueda');
        $this->auditoria("PLAN CREADO ID " . $plan->id);
        return redirect()->action([SegConvenioController::class, 'show'], ['convenio' => $idConvenio])
            ->with('success', 'Plan creado correctamente.');
    }

    /**
     * Display the specified resource.
     */
    public function show(SegPlan $segPlan)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $segPlan = SegPlan::with('coberturas')->findOrFail($id);
        $coberturas = SegCobertura::all();
        $convenios = SegConvenio::all();
        $condiciones = SegCondicion::all();
        return view('seguros.planes.edit', compact('segPlan', 'coberturas', 'convenios', 'condiciones'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, SegPlan $segPlan)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(SegPlan $plan)
    {
        SegPlan_cobertura::where('plan_id', $plan->id)->delete();
        $plandelete = SegPlan::findOrFail($plan->id);
        $plandelete->delete();
        $this->auditoria("PLAN ELIMINADO ID" . $plan->id);
        return redirect()->back()->with('success', 'Plan eliminado correctamente.');
    }

    public function getCondicion($edad)
    {
        if ($edad >= 0 && $edad <= 17) {
            return 1; // Asegurados desde 0 años hasta 17 años
        } elseif ($edad >= 18 && $edad <= 65) {
            return 2; // Asegurados desde 18 años hasta 65 años
        } elseif ($edad >= 66 && $edad <= 70) {
            return 3; // Asegurados desde 66 años hasta 70 años
        } elseif ($edad >= 71 && $edad <= 80) {
            return 4; // Asegurados desde 71 años hasta 80 años
        } elseif ($edad >= 81 && $edad <= 89) {
            return 5; // Asegurados desde 81 años hasta 89 años
        } elseif ($edad >= 90) {
            return 6; // Asegurados desde 90 años
        } else {
            return null;
        }
    }
}
