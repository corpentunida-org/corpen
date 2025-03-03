<?php

namespace App\Http\Controllers\Seguros;

use App\Http\Controllers\Controller;
use App\Models\Seguros\SegConvenio;
use App\Models\Seguros\SegCondicion;
use App\Models\Seguros\SegPlan;
use App\Models\Seguros\SegPlan_cobertura;
use App\Http\Controllers\AuditoriaController;
use Illuminate\Http\Request;

class SegConvenioController extends Controller
{
    private function auditoria($accion){
        $auditoriaController = app(AuditoriaController::class);
        $auditoriaController->create($accion, "SEGUROS");
    }

    public function index()
    {
        $convenios = SegConvenio::all();
        return view('seguros.convenio.index', compact('convenios'));
    }
    public function show($id)
    {
        $c = SegConvenio::findOrFail($id);
        $idConvenio = $c->idConvenio;
        $convenio = SegConvenio::with(['plan.condicion'])
            ->where('idConvenio', $idConvenio)
            ->first();
        $planes = $convenio->plan->groupBy('condicion');
        /* dd($convenio); */
        return view('seguros.convenio.show', compact('convenio', 'planes'));
    }

    public function create(Request $request)
    {
        $idConvenio = $request->get('id');  // Recibimos el id del convenio original
        $convenio = SegConvenio::with(['plan.condicion'])->find($idConvenio);  // Cargamos el convenio original
        $planes = $convenio ? $convenio->plan->groupBy('condicion') : collect();  // Agrupamos los planes por condición
        $condiciones = SegCondicion::all();
        return view('seguros.convenio.create', compact('convenio', 'planes', 'condiciones'));
    }

    public function store(Request $request) {
        $idConvenio = $request->anio . $request->idAseguradora;
        $validacion = SegConvenio::where('idConvenio', $idConvenio)->exists();
        if ($validacion) {
            dd($idConvenio);
            return redirect()->back()->with('error', 'Ya existe un convenio con el mismo año y aseguradora');
        }
        $convenio = SegConvenio::create([
            'idConvenio' => $idConvenio,
            'seg_proveedor_id' => $request->proveedor,
            'idAseguradora' => $request->idAseguradora,
            'nombre' => strtoupper($request->nombre) . " " . $request->anio,
            'fecha_inicio' => $request->fechaInicio,
            'fecha_fin' => $request->fechaFin,
        ]);
        $planes = $request->input('planes');
        foreach ($planes as $planId => $planData) {
            $nuevoPlan = SegPlan::create([
                'seg_convenio_id' => $convenio->idConvenio, 
                'name' => strtoupper($planData['name']),
                'valor' => $planData['vasegurado'],
                'prima' => $planData['vprima'],
                'condicion_id' => $planData['condicion'],
            ]);            
           
            $coberturas = SegPlan_cobertura::where('plan_id', $planId)->get();
            foreach ($coberturas as $cobertura) {
                SegPlan_cobertura::create([
                    'plan_id' => $nuevoPlan->id,
                    'cobertura_id' => $cobertura->cobertura_id,
                    'valorAsegurado' => $cobertura->valorAsegurado,
                    'valorCobertura' => $cobertura->valorCobertura,
                ]);
            }
        }
        $this->auditoria("CONVENIO CREADO " . $convenio->nombre);
        return redirect()->route('seguros.convenio.index')->with('success', 'Convenio creado correctamente');
    }
}