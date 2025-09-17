<?php

namespace App\Http\Controllers\Seguros;

use App\Http\Controllers\Controller;
use App\Models\Seguros\SegConvenio;
use App\Models\Seguros\SegCondicion;
use App\Models\Seguros\SegPlan;
use App\Models\Seguros\SegPoliza;
use App\Models\Seguros\SegPlan_cobertura;
use App\Http\Controllers\AuditoriaController;
use Carbon\Carbon;
use Illuminate\Http\Request;

class SegConvenioController extends Controller
{
    private function auditoria($accion)
    {
        $auditoriaController = app(AuditoriaController::class);
        $auditoriaController->create($accion, "SEGUROS");
    }

    public function index()
    {
        $convenios = SegConvenio::orderBy('fecha_inicio', 'asc')->get();
        return view('seguros.convenio.index', compact('convenios'));
    }
    public function show($id)
    {
        $c = SegConvenio::findOrFail($id);
        $idConvenio = $c->idConvenio;
        $convenio = SegConvenio::with(['plan.condicioncorpen'])
            ->where('idConvenio', $idConvenio)
            ->first();
        $planes = $convenio->plan->filter(function ($plan) {
                return $plan->condicioncorpen !== null && $plan->condicioncorpen->descripcion !== null;})
            ->groupBy(function ($plan) {return $plan->condicioncorpen->descripcion;});
        return view('seguros.convenio.show', compact('convenio', 'planes'));
    }

    public function create(Request $request)
    {
        $idConvenio = $request->get('id');
        $convenio = SegConvenio::with(['plan.condicion'])->find($idConvenio);
        $planes = $convenio ? $convenio->plan->groupBy('condicion') : collect();
        $condiciones = SegCondicion::all();
        return view('seguros.convenio.create', compact('convenio', 'planes', 'condiciones'));
    }

    public function store(Request $request)
    {

        $idConvenio = $request->anio . $request->idAseguradora;
        $validacion = SegConvenio::where('idConvenio', $idConvenio)->exists();
        if ($validacion) {
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
                $coberturaData = [
                    'plan_id' => $nuevoPlan->id,
                    'cobertura_id' => $cobertura->cobertura_id,
                    'porcentaje' => $cobertura->porcentaje,
                ];
                if ($cobertura->cobertura_id == 1) {
                    $coberturaData['valorAsegurado'] = $nuevoPlan->valor;
                    $coberturaData['valorCobertura'] = $nuevoPlan->prima;
                }
                SegPlan_cobertura::create($coberturaData);
            }
        }
        $this->auditoria("CONVENIO CREADO " . $convenio->nombre);
        if ($request->has('CheckVigencia')) {
            $this->updateVigencia($request->idAseguradora, $request->anio);
            SegConvenio::where('idAseguradora', $request->idAseguradora)
                ->where('vigente', true)
                ->update(['vigente' => false]);
            SegConvenio::where('idConvenio', $idConvenio)
                ->update(['vigente' => true]);
            SegPlan::where('seg_convenio_id', 'like', '%' . $request->idAseguradora)
                ->update(['vigente' => false]);
            SegPlan::where('seg_convenio_id', $idConvenio)
                ->update(['vigente' => true]);
            $this->auditoria("Novedad en polizas, convenios y planes con nueva vigencia " . $convenio->nombre);
        }

        return redirect()->route('seguros.convenio.index')->with('success', 'Convenio creado correctamente');
    }


    private function updateVigencia($convenioid, $convenioanio)
    {
        $polizas = SegPoliza::where('seg_convenio_id', $convenioid)
            ->where('active', true)->where('reclamacion', 0)->get();
        foreach ($polizas as $poliza) {
            
            $planOriginal = SegPlan::find($poliza->seg_plan_id);
            $plan = SegPlan::where('name', $planOriginal->name)
                ->where('condicion_id', $planOriginal->condicion_id)
                ->where('seg_convenio_id', $convenioanio . $convenioid)->first();
            if ($plan) {
                if ($poliza->extra_prima != 0) {
                    $planprima = (($plan->prima_aseguradora * $poliza->extra_prima) / 100) + $plan->prima_aseguradora;
                } else {
                    $planprima = $plan->prima_aseguradora;
                }
                $poliza->update([
                    'seg_plan_id' => $plan->id,
                    'valor_asegurado' => $plan->valor,
                    'valor_prima' => $planprima
                ]);
            }
        }
    }
}