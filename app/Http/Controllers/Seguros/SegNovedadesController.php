<?php

namespace App\Http\Controllers\Seguros;

use App\Http\Controllers\Controller;
use App\Models\Seguros\SegNovedades;
use App\Models\Seguros\SegAsegurado;
use App\Models\Seguros\SegPlan;
use Illuminate\Http\Request;

class SegNovedadesController extends Controller
{
    public function index()
    {
        $update = SegAsegurado::where('parentesco', 'AF')->whereNull('valorpAseguradora')
        ->with(['tercero','polizas.plan'])->get();
        $novedades = SegNovedades::all();
        return view('seguros.novedades.index', compact('novedades','update'));
    }

    public function create(Request $request)
    {
        $id = $request->query('a');
        $asegurado = SegAsegurado::where('cedula', $id)->with(['tercero','terceroAF','polizas.plan'])->first();
        $fechaNacimiento = $asegurado->tercero->fechaNacimiento;
        $edad = date_diff(date_create($fechaNacimiento), date_create('today'))->y;
        $condicion = app(SegPlanController::class)->getCondicion($edad);
        $planes = SegPlan::where('condicion_id', $condicion)
                ->where('vigente', true)->with(['convenio'])->get();
        
        return view('seguros.novedades.create', compact('asegurado', 'planes'));
    }
}
