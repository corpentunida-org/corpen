<?php

namespace App\Http\Controllers\Seguros;

use App\Http\Controllers\Controller;
use App\Models\Seguros\SegBeneficios;
use App\Models\Seguros\SegCondicion;
use App\Models\Seguros\SegNovedades;
use App\Models\Seguros\SegAsegurado;
use App\Models\Seguros\SegPlan;
use App\Models\Seguros\SegPoliza;
use App\Models\Seguros\SegCambioEstadoNovedad;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\AuditoriaController;
use Illuminate\Support\Facades\DB;

class SegNovedadesController extends Controller
{
    private function auditoria($accion)
    {
        $auditoriaController = app(AuditoriaController::class);
        $auditoriaController->create($accion, 'SEGUROS');
    }
    public function index()
    {
        /* $update = SegAsegurado::where('parentesco', 'AF')
            ->whereHas('polizas', function ($query) {
                $query->whereNull('valorpagaraseguradora')
                    ->orWhere('valorpagaraseguradora', ' ');
            })->get();
        $novedades = SegNovedades::with(['tercero', 'asegurado.terceroAF', 'plan'])->get();
        return view('seguros.novedades.index', compact('novedades', 'update')); */

        $solicitud = SegNovedades::where('estado', 1)
            ->with(['tercero', 'cambiosEstado'])
            ->get();
        $radicado = SegNovedades::where('estado', 2)->with('estadoNovedad')->get();
        $negado = SegNovedades::where('estado', 4)->with('estadoNovedad')->get();
        return view('seguros.novedades.index', compact('solicitud', 'radicado', 'negado'));
    }

    /* public function create(Request $request)
    {
        $id = $request->query('a');
        $isactive = SegPoliza::where('seg_asegurado_id', $id)->where('seg_convenio_id', '23055455')->first();
        if (!$isactive->active) {
            abort(403, 'La póliza está inactiva.');
        }
        $asegurado = SegAsegurado::where('cedula', $id)->with(['tercero', 'terceroAF', 'polizas.plan'])->first();

        $edad = date_diff(date_create($asegurado->tercero_preferido->fec_nac), date_create('today'))->y;
        $idcondicion = app(SegPlanController::class)->getCondicion($edad);

        $planes = SegPlan::where('condicion_corpen', $idcondicion)
            ->where('vigente', true)->with(['convenio'])->get();

        $condicion = SegCondicion::where('id', $idcondicion)->first(['descripcion']);

        $grupoFamiliar = SegAsegurado::where('Titular', $asegurado->titular)->with('tercero', 'polizas.plan.coberturas')->get();
        $totalPrima = DB::table('SEG_polizas')
            ->whereIn('seg_asegurado_id', $grupoFamiliar->pluck('cedula'))
            ->sum('valor_prima');

        $reclamaciones = SegReclamaciones::where('cedulaAsegurado', $asegurado->cedula)->with(['cobertura', 'diagnostico'])->get();

        return view('seguros.novedades.create', compact('asegurado', 'planes', 'condicion', 'grupoFamiliar', 'totalPrima', 'reclamaciones'));
    } */

    public function create()
    {
        return view('seguros.novedades.create');
    }

    public function show(Request $request)
    {
        $id = $request->query('a');
        $isactive = SegPoliza::where('seg_asegurado_id', $id)->where('seg_convenio_id', '23055455')->first();
        if (!$isactive || !$isactive->active) {
            return back()->with('error', 'La póliza no existe o está inactiva.');
        }
        $asegurado = SegAsegurado::where('cedula', $id)
            ->with(['tercero', 'terceroAF', 'polizas.plan'])
            ->first();
        $edad = date_diff(date_create($asegurado->tercero_preferido->fec_nac), date_create('today'))->y;
        $idcondicion = app(SegPlanController::class)->getCondicion($edad);

        $planes = SegPlan::where('condicion_corpen', $idcondicion)
            ->where('vigente', true)
            ->with(['convenio'])
            ->get();

        $condicion = SegCondicion::where('id', $idcondicion)->first(['descripcion']);
        $tab = $request->activeTab ?? 'generalTab';
        $grupoFamiliar = SegAsegurado::where('Titular', $asegurado->titular)->with('tercero', 'polizas.plan.coberturas')->get();
        $totalPrima = DB::table('SEG_polizas')->whereIn('seg_asegurado_id', $grupoFamiliar->pluck('cedula'))->sum('valor_prima');
        $totalPrimaCorpen = DB::table('SEG_polizas')->whereIn('seg_asegurado_id', $grupoFamiliar->pluck('cedula'))->sum('primapagar');
        //$reclamaciones = SegReclamaciones::where('cedulaAsegurado', $asegurado->cedula)->with(['cobertura', 'diagnostico'])->get();
        return view('seguros.novedades.create', compact('asegurado', 'planes', 'condicion', 'grupoFamiliar', 'totalPrima', 'totalPrimaCorpen'))->with('activeTab', $tab);
    }

    public function store(Request $request)
    {
        $plan = SegPlan::findOrFail($request->planid);
        $novedad = SegNovedades::create([
            'id_poliza' => $request->id_poliza,
            'id_asegurado' => $request->asegurado,
            'tipo' => $request->tipoNovedad,
            'estado' => $request->estado,
            'id_plan' => $plan->id,
            'valorAsegurado' => $plan->valor,
            'primaAseguradora' => $plan->prima_aseguradora,
            'primaCorpen' => $request->primacorpen,
            'extraprima' => $request->extra_prima,
        ]);
        SegCambioEstadoNovedad::create([
            'novedad' => $novedad->id,
            'estado' => $request->estado,
            'observaciones' => strtoupper($request->observaciones),
            'fechaIncio' => Carbon::now()->toDateString(),
        ]);
        $accion = 'novedad en poliza  ' . $request->id_poliza . ' Asegurado ' . $request->asegurado;
        $this->auditoria($accion);
        return redirect()->route('seguros.novedades.index')->with('success', 'Novedad registrada correctamente');
    }

    public function edit($id)
    {
        $novedad = SegNovedades::with(['poliza', 'estadoNovedad', 'cambiosEstado'])->findOrFail($id);
        if ($novedad->estado != 1) {
            return back()->with('error', 'La novedad no esta en estado solicitud. No se puede editar.');
        }
        return view('seguros.novedades.edit', compact('novedad'));
    }

    public function update(Request $request, $id)
    {
        if ($request->estado == 3) {
            if ($request->tipo === 'INGRESO') {
                dd('es ingreso', $request->all());

            } elseif ($request->tipo === 'MODIFICACIÓN') {
                dd('es modificacion', $request->all());
                $poliza->update([
                    'valor_prima' => $plan->primaAseguradora,
                    'seg_plan_id' => $request->planid,
                    'extra_prima' => $request->extraprima,
                    'primapagar' => $request->primaPagar,
                ]);
                SegNovedades::where('id', $id)->update([
                    'estado' => $request->estado,
                ]);
                SegCambioEstadoNovedad::create([
                    'novedad' => $id,
                    'estado' => $request->estado,
                    'observaciones' => strtoupper($request->observaciones),
                    'fechaCierre' => Carbon::now()->toDateString(),
                ]);
                return redirect()->route('seguros.novedades.index')->with('success', 'Novedad aprobada y póliza actualizada correctamente');
            }
        }
    }
}
