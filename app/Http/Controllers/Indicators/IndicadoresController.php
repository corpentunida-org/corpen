<?php

namespace App\Http\Controllers\Indicators;

use App\Http\Controllers\Controller;
use App\Models\Indicators\IndIndicadores;
use App\Models\Indicators\IndUsuarios;
use App\Models\Soportes\ScpSoporte;
use App\Models\Flujo\Workflow;
use App\Models\Archivo\GdoEmpleado;
use App\Models\User;
use App\Models\Indicators\IndRegistroInformes;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Storage;

class IndicadoresController extends Controller
{
    public function index()
    {
        $indicators = $this->dataIndicadores();
        $lastReport = IndRegistroInformes::latest()->first();
        $totalIndicadores = IndRegistroInformes::count() - 3;
        $indicadoresAlcanzados = 0;
        foreach ($indicators as $ind) {
            if ($ind->indicador_calculado !== null) {
                $totalIndicadores++;
                $meta = $this->parseMeta($ind->meta);
                if ($ind->indicador_calculado >= $meta['valor']) {
                    $indicadoresAlcanzados++;
                }
            }
        }
        $promedioAlcanzados = $totalIndicadores > 0 ? ($indicadoresAlcanzados / $totalIndicadores) * 100 : 0;
        $promedioAlcanzados = round($promedioAlcanzados, 2);
        return view('indicators.index', compact('indicators', 'lastReport','promedioAlcanzados'));
    }

    public function dataIndicadores()
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
                        "TIMESTAMPDIFF(DAY, s.timestam, o.fecha_cierre) <=
                        CASE s.id_scp_prioridad WHEN 3 THEN 15 WHEN 2 THEN 10 WHEN 1 THEN 5 ELSE 0 END",)->count();
                return ($totalDentroSLA / ScpSoporte::where('estado', 4)->count()) * 100;
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
            10 => function () {
                $users = User::where('type', null)->count();
                return ($users / GdoEmpleado::count()) * 100;
            },
        ];

        foreach ($indicators as $ind) {
            $ind->indicador_calculado = isset($calculos[$ind->id]) ? $calculos[$ind->id]($ind) : null;
        }

        return $indicators;
    }

    public function show($id)
    {
        $indicator = IndIndicadores::findOrFail($id);
        return view('indicators.show', compact('indicator'));
    }

    public function descargarInforme()
    {
        $user = Auth::user();
        $indicators = $this->dataIndicadores();

        $pdf = Pdf::loadView('indicators.informepdf', compact('indicators'))->setPaper('A4', 'portrait');
        $fileName = 'InformeTIC_' . now()->format('Ymd_His') . '.pdf';
        Storage::disk('s3')->put('corpentunida/indicators/' . $fileName, $pdf->output());
        IndRegistroInformes::create([
            'archivo' => 'corpentunida/indicators/' . $fileName,
            'usuario' => auth()->id(),
            'fecha_descarga' => now(),
        ]);
        return $pdf->download('informe_indicadores.pdf');
    }

    private function parseMeta(string $meta): array
    {
        $meta = trim(mb_strtolower($meta));

        // ≥ 80%
        if (preg_match('/≥\s*(\d+(\.\d+)?)%/', $meta, $m)) {
            return ['op' => '>=', 'valor' => (float) $m[1], 'unidad' => '%'];
        }

        // ≥ 4.2 / 5
        if (preg_match('/≥\s*(\d+(\.\d+)?)\s*\/\s*(\d+(\.\d+)?)/', $meta, $m)) {
            return ['op' => '>=', 'valor' => ((float) $m[1] / (float) $m[3]) * 100, 'unidad' => '%'];
        }

        // ≤ 6 horas
        if (preg_match('/≤\s*(\d+(\.\d+)?)\s*horas?/', $meta, $m)) {
            return ['op' => '<=', 'valor' => (float) $m[1], 'unidad' => 'horas'];
        }

        // fallback numérico
        if (is_numeric($meta)) {
            return ['op' => '>=', 'valor' => (float) $meta, 'unidad' => null];
        }

        return [];
    }
}
