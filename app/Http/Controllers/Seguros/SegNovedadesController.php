<?php

namespace App\Http\Controllers\Seguros;

use App\Http\Controllers\Controller;
use App\Models\Seguros\SegBeneficios;
use App\Models\Seguros\SegCondicion;
use App\Models\Seguros\SegNovedades;
use App\Models\Seguros\SegAsegurado;
use App\Models\Seguros\SegPlan;
use App\Models\Seguros\SegPoliza;
use App\Models\Seguros\SegReclamaciones;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\AuditoriaController;
use Illuminate\Support\Facades\DB;
use PHPUnit\Event\Test\PrintedUnexpectedOutputSubscriber;

class SegNovedadesController extends Controller
{
    private function auditoria($accion)
    {
        $auditoriaController = app(AuditoriaController::class);
        $auditoriaController->create($accion, "SEGUROS");
    }
    public function index()
    {
        $update = SegAsegurado::where('parentesco', 'AF')
            ->whereHas('polizas', function ($query) {
                $query->whereNull('valorpagaraseguradora')
                    ->orWhere('valorpagaraseguradora', ' ');
            })->get();
        $novedades = SegNovedades::with(['tercero', 'asegurado.terceroAF', 'plan'])->get();
        return view('seguros.novedades.index', compact('novedades', 'update'));
    }

    public function create(Request $request)
    {
        $id = $request->query('a');
        $asegurado = SegAsegurado::where('cedula', $id)->with(['tercero', 'terceroAF.asegurados', 'polizas.plan'])->first();
        $fechaNacimiento = $asegurado->tercero->fechaNacimiento;
        $edad = date_diff(date_create($fechaNacimiento), date_create('today'))->y;
        $idcondicion = app(SegPlanController::class)->getCondicion($edad);

        $planes = SegPlan::where('condicion_id', $idcondicion)
            ->where('vigente', true)->with(['convenio'])->get();

        $condicion = SegCondicion::where('id', $idcondicion)->first(['descripcion']);

        $grupoFamiliar = SegAsegurado::where('Titular', $asegurado->titular)->with('tercero', 'polizas.plan.coberturas')->get();
        $totalPrima = DB::table('SEG_polizas')
            ->whereIn('seg_asegurado_id', $grupoFamiliar->pluck('cedula'))
            ->sum('valor_prima');

        $reclamaciones = SegReclamaciones::where('cedulaAsegurado', $asegurado->cedula)->with(['cobertura', 'diagnostico'])->get();

        return view('seguros.novedades.create', compact('asegurado', 'planes', 'condicion', 'grupoFamiliar', 'totalPrima', 'reclamaciones'));
    }

    public function store(Request $request)
    {
        $poliza = SegPoliza::findOrFail($request->id_poliza);
        $plan = SegPlan::findOrFail($request->planid);
        $asegurado = SegAsegurado::where('cedula', $request->asegurado)->first();
        $now = Carbon::now();
        $data = [
            'fecha_novedad' => $now->toDateString(),
            'extra_prima' => $request->extra_prima,
            'seg_plan_id' => $request->planid,
            'descuento' => $request->valordescuento,
            'descuentopor' => $request->descuento,
            'valor_asegurado' => $plan->valor,
            'valorpagaraseguradora' => $request->valorpagaraseguradora,
        ];
        $valorprima = $plan->prima;
        if (blank($request->extra_prima)) {
            $data['valor_prima'] = $valorprima;
        } else {
            $valorprima = $plan->prima * (1 + $request->extra_prima / 100);
            $data['valor_prima'] = intval($valorprima);
        }
        $poliza->update($data);
        $asegurado->update([
            'valorpAseguradora' => $request->valorpagaraseguradora,
        ]);
        if (!empty($valordescuento) || !empty($descuento)) {
            SegBeneficios::create([
                'cedulaAsegurado' => $request->asegurado,
                'poliza' => $request->id_poliza,
                'porcentajeDescuento' => $request->descuento,
                'valorDescuento' => $request->valordescuento,
                'observaciones' => strtoupper($request->observaciones),
            ]);
        }
        if ($poliza && $asegurado) {
            SegNovedades::create([
                'id_poliza' => $request->id_poliza,
                'id_asegurado' => $request->asegurado,
                'valorpagar' => $request->valorpagaraseguradora ?? null,
                'valorPrimaPlan' => $valorprima,
                'plan' => $request->planid,
                'fechaNovedad' => $now->toDateString(),
                'valorAsegurado' => $plan->valor,
                'observaciones' => $request->observaciones,
            ]);
            $accion = "novedad en poliza  " . $request->id_poliza . " Asegurado " . $request->asegurado;
            $this->auditoria($accion);
            $url = route('seguros.poliza.show', ['poliza' => 'ID']) . '?id=' . $request->asegurado;
            return redirect()->to($url)
                ->with('success', 'Novedad registrada correctamente');
        }
        return redirect()->back()->with('error', 'No se pudo registrar la novedad.');
    }
}
