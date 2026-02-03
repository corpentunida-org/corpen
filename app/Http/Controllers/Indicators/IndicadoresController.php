<?php

namespace App\Http\Controllers\Indicators;

use App\Http\Controllers\Controller;
use App\Models\Indicators\IndIndicadores;
use App\Models\Indicators\IndUsuarios;
use App\Models\Soportes\ScpSoporte;
use App\Models\Flujo\Workflow;
use App\Models\Archivo\GdoEmpleado;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class IndicadoresController extends Controller
{
    public function index()
    {        
        $indicators = IndIndicadores::all();
        $calculos = [
            1 => function () {
                $usuarios = IndUsuarios::whereRaw("CAST(SUBSTRING_INDEX(puntaje, '/', 1) AS UNSIGNED) > 3")->count();
                return ($usuarios / GdoEmpleado::count()) * 100;
            },
            2 => function () {
                $satisfaccion = IndUsuarios::whereRaw('JSON_VALID(respuestas)')->selectRaw("SUM(CAST(JSON_UNQUOTE(JSON_EXTRACT(respuestas, '$[5]')) AS UNSIGNED)) as total")->value('total');
                return $satisfaccion / IndUsuarios::count();
            },
            3 => function () {
                $satisnuevastic = IndUsuarios::whereRaw("JSON_UNQUOTE(JSON_EXTRACT(respuestas, '$[6]')) != 'n/a'")->selectRaw("SUM(CAST(JSON_UNQUOTE(JSON_EXTRACT(respuestas, '$[6]')) AS UNSIGNED)) / COUNT(*) as promedio")->value('promedio');
                return $satisnuevastic;
            },
            5 => function () {
                $totalDentroSLA = ScpSoporte::from('scp_soportes as s')
                    ->joinSub(DB::table('scp_observaciones')->select('id_scp_soporte', DB::raw('MIN(timestam) as fecha_cierre'))->where('id_scp_estados', 4)->groupBy('id_scp_soporte'), 'o', 'o.id_scp_soporte', '=', 's.id')
                    ->whereRaw(
                        "
                        TIMESTAMPDIFF(DAY, s.timestam, o.fecha_cierre) <=
                        CASE s.id_scp_prioridad
                            WHEN 3 THEN 15
                            WHEN 2 THEN 10
                            WHEN 1 THEN 5
                            ELSE 0
                        END
                    ",)->count();                    
                    return ($totalDentroSLA / ScpSoporte::where('estado',4)->count()) * 100;
            },
            6 => function () {
                $tiempoPromedioAlta = DB::table('scp_soportes as s')->join('scp_observaciones as o', 'o.id_scp_soporte', '=', 's.id')->where('s.id_scp_prioridad', 1)->selectRaw('AVG(TIMESTAMPDIFF(MINUTE,s.created_at,o.created_at)) as promedio')->groupBy('s.id')->value('promedio');
                return $tiempoPromedioAlta;
            },
            7 => function () {
                $tiempoPromedioAlta = DB::table('scp_soportes as s')->join('scp_observaciones as o', 'o.id_scp_soporte', '=', 's.id')->where('s.id_scp_prioridad', 2)->selectRaw('AVG(TIMESTAMPDIFF(MINUTE,s.created_at,o.created_at)) as promedio')->groupBy('s.id')->value('promedio');
                return $tiempoPromedioAlta;
            },
            8 => function () {
                $tiempoPromedioAlta = DB::table('scp_soportes as s')->join('scp_observaciones as o', 'o.id_scp_soporte', '=', 's.id')->where('s.id_scp_prioridad', 3)->selectRaw('AVG(TIMESTAMPDIFF(MINUTE,s.created_at,o.created_at)) as promedio')->groupBy('s.id')->value('promedio');
                return $tiempoPromedioAlta;
            },
            9 => function () {
                $proyectosDigitalizados = Workflow::whereIn('estado', ['activo', 'completado'])->count();
                return ($proyectosDigitalizados / Workflow::count()) * 100;
            },
        ];

        foreach ($indicators as $ind) {
            $ind->indicador_calculado = isset($calculos[$ind->id]) ? $calculos[$ind->id]($ind) : null;
        }

        return view('indicators.index', compact('indicators'));
    }
}
