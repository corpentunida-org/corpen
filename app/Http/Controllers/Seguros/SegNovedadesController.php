<?php

namespace App\Http\Controllers\Seguros;

use App\Http\Controllers\Controller;
use App\Models\Seguros\SegCondicion;
use App\Models\Seguros\SegNovedades;
use App\Models\Seguros\SegAsegurado;
use App\Models\Seguros\SegPlan;
use App\Models\Seguros\SegPoliza;
use Carbon\Carbon;
use Illuminate\Http\Request;

class SegNovedadesController extends Controller
{
    public function index()
    {
        $update = SegAsegurado::where('parentesco', 'AF')
        ->whereHas('polizas', function($query) {$query->whereNull('valorpagaraseguradora')
              ->orWhere('valorpagaraseguradora', ' ');})->get();
        $novedades = SegNovedades::with(['tercero', 'asegurado.terceroAF','plan'])->get();
        return view('seguros.novedades.index', compact('novedades', 'update'));
    }

    public function create(Request $request)
    {
        $id = $request->query('a');
        $asegurado = SegAsegurado::where('cedula', $id)->with(['tercero', 'terceroAF', 'polizas.plan'])->first();
        $fechaNacimiento = $asegurado->tercero->fechaNacimiento;
        $edad = date_diff(date_create($fechaNacimiento), date_create('today'))->y;
        $idcondicion = app(SegPlanController::class)->getCondicion($edad);

        $planes = SegPlan::where('condicion_id', $idcondicion)
            ->where('vigente', true)->with(['convenio'])->get();

        $condicion = SegCondicion::where('id', $idcondicion)->first(['descripcion']);

        return view('seguros.novedades.create', compact('asegurado', 'planes', 'condicion'));
    }

    public function store(Request $request)
    {
        $poliza = SegPoliza::findOrFail($request->id_poliza);
        $asegurado = SegAsegurado::where('cedula',$request->asegurado)->first();
        $now = Carbon::now();

        $poliza->update([
            'fecha_novedad' => $now->toDateString(),
            'extra_prima' => $request->extra_prima,
            'seg_plan_id' => $request->planid,
            'descuento' => $request->descuento,
            'valor_prima' => (int) str_replace(',', '', $request->valorPrima),
            'valor_asegurado' => (int) str_replace(',', '', $request->valorAsegurado),
            'valorpagaraseguradora' => $request->valorpagaraseguradora,
        ]);
        $asegurado->update([
            'valorpAseguradora' => $request->valorpagaraseguradora,
        ]);

        if ($poliza && $asegurado) {
            SegNovedades::create([
                'id_asegurado' => $request->asegurado,
                'id_poliza' => $request->id_poliza,
                'valorpAseguradora' => $request->valorpagaraseguradora,
                'valorPrimaPlan' => $request->valorPrima,
                'planNuevo' => $request->planid,
                'fechaNovedad' => $now->toDateString(),
                'valorAsegurado' => (int) str_replace(',', '', $request->valorAsegurado),
                'observaciones' => $request->observaciones,
            ]);
            return redirect()->route('seguros.novedades.index')->with('success', 'Novedad registrada correctamente');
        }
        return redirect()->back()->with('error', 'No se pudo registrar la novedad.');
    }
}
