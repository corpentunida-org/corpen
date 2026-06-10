<?php

namespace App\Http\Controllers\Indicators;

use App\Http\Controllers\Controller;
use App\Models\Indicators\IndIndicadores;
use App\Models\Indicators\IndUsuarios;
use App\Models\Soportes\ScpSoporte;
use App\Models\Flujo\Workflow;
use App\Models\Archivo\GdoEmpleado;
use App\Models\Inventario\InvMovimientoDetalle;
use App\Models\Inventario\InvActivo;
use App\Models\User;
use App\Models\Archivo\GdoArea;
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
        try {
            $indicators = $this->dataIndicadores();
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
        $lastReport = IndRegistroInformes::latest()->first();
        $totalIndicadores = IndRegistroInformes::count() - 3;
        $indicadoresAlcanzados = 0;
        /*foreach ($indicators as $grupo) {
            foreach ($grupo as $ind) {
                if ($ind->indicador_calculado !== null) {
                    $totalIndicadores++;
                    $meta = $this->parseMeta($ind->meta);
                    if ($ind->indicador_calculado >= $meta['valor']) {
                        $indicadoresAlcanzados++;
                    }
                }
            }
        }*/
        $promedioAlcanzados = $totalIndicadores > 0 ? ($indicadoresAlcanzados / $totalIndicadores) * 100 : 0;
        $promedioAlcanzados = round($promedioAlcanzados, 2);
        return view('indicators.index', compact('indicators', 'lastReport', 'promedioAlcanzados'));
    }

    public function dataIndicadores()
    {
        $indicators = IndIndicadores::with('arearel')
            ->get()
            ->groupBy(function ($item) {
                return $item->arearel->nombre ?? ' ';
            });
        //dd($indicators);
        $calculos = [
            1 => function () {
                $usuarios = IndUsuarios::whereRaw("CAST(SUBSTRING_INDEX(puntaje, '/', 1) AS UNSIGNED) > 3")->count();
                return ($usuarios / GdoEmpleado::where('id', '>', 11)->count()) * 100;
            },
            2 => function () {
                $satisfaccion = IndUsuarios::whereRaw('JSON_VALID(respuestas)')->selectRaw("SUM(CAST(JSON_UNQUOTE(JSON_EXTRACT(respuestas, '$[5]')) AS UNSIGNED)) as total")->value('total');
                return $satisfaccion / IndUsuarios::count();
            },
            3 => function () {
                $satisnuevastic = IndUsuarios::whereRaw("JSON_UNQUOTE(JSON_EXTRACT(respuestas, '$[6]')) != 'n/a'")->selectRaw("SUM(CAST(JSON_UNQUOTE(JSON_EXTRACT(respuestas, '$[6]')) AS UNSIGNED)) / COUNT(*) as promedio")->value('promedio');
                return $satisnuevastic;
            },
            4 => function () {
                $totalActivos = InvActivo::count();
                $activosAsignados = InvMovimientoDetalle::where('id_estado', 9)->count();
                return ($activosAsignados / $totalActivos) * 100;
            },
            5 => function () {
                /* $totalDentroSLA = ScpSoporte::from('scp_soportes as s')
                    ->joinSub(DB::table('scp_observaciones')->select('id_scp_soporte', DB::raw('MIN(timestam) as fecha_cierre'))->where('id_scp_estados', 4)->groupBy('id_scp_soporte'), 'o', 'o.id_scp_soporte', '=', 's.id')
                    ->whereRaw("TIMESTAMPDIFF(DAY, s.timestam, o.fecha_cierre) <= CASE s.id_scp_prioridad WHEN 3 THEN 30 WHEN 2 THEN 20 WHEN 1 THEN 10 ELSE 0 END",)->count(); */
                $totalDentroSLA = ScpSoporte::from('scp_soportes as s')
                    ->joinSub(DB::table('scp_observaciones')->select('id_scp_soporte', DB::raw('MIN(timestam) as fecha_cierre'))->where('id_scp_estados', 4)->groupBy('id_scp_soporte'), 'o', 'o.id_scp_soporte', '=', 's.id')
                    ->count();
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
            11 => function () {
                $globalCounts = Workflow::where('estado', '!=', 'borrador')->where('estado', '!=', 'archivado')->selectRaw('estado, count(*) as total')->groupBy('estado')->pluck('total', 'estado');
                return (($globalCounts['completado'] ?? 0) / $globalCounts->sum()) * 100;
            },
        ];

        foreach ($indicators as $grupo) {
            foreach ($grupo as $ind) {
                if (isset($calculos[$ind->id])) {
                    $ind->indicador_calculado = $calculos[$ind->id]($ind);
                } elseif (!empty($ind->consulta_bd)) {
                    $ind->indicador_calculado = isset($calculos[$ind->id]) ? $calculos[$ind->id]($ind) : null;
                    try {
                        $resultado = collect(DB::select($ind->consulta_bd))->first();
                        $ind->indicador_calculado = $resultado ? array_values((array) $resultado)[0] ?? null : null;
                    } catch (\Exception $e) {
                        throw new \Exception('Error en el indicador "' . $ind->nombre . '" - ' . $e->getMessage());
                    }
                }
            }
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

    public function create()
    {
        $areas = GdoArea::select('id', 'nombre')->get();
        $responsables = GdoEmpleado::selectRaw(
            "id,
                CONCAT(
                    nombre1, ' ',
                    IFNULL(nombre2, ''), ' ',
                    apellido1, ' ',
                    IFNULL(apellido2, '')
                ) as nombre
            ",
        )
            ->where('id', '>=', 11)
            ->get();
        return view('indicators.create', compact('areas', 'responsables'));
    }

    public function store(Request $request)
    {
        IndIndicadores::create([
            'nombre' => $request->name,
            'calculo' => $request->calculation,
            'meta' => $request->goal,
            'frecuencia' => $request->frecuencia,
            'responsable' => $request->responsible,
            'area' => $request->area,
            'consulta_bd' => $request->consultasql,
        ]);

        return redirect()->route('indicators.indicadores.index')->with('success', 'Indicador creado exitosamente.');
    }

    public function edit($id)
    {
        $indicador = IndIndicadores::findOrFail($id);
        $areas = GdoArea::select('id', 'nombre')->get();
        $responsables = GdoEmpleado::selectRaw("id,CONCAT(nombre1, ' ',IFNULL(nombre2, ''), ' ',apellido1, ' ',IFNULL(apellido2, '')) as nombre")->where('id', '>=', 11)->get();

        return view('indicators.edit', compact('indicador', 'areas', 'responsables'));
    }

    public function update(Request $request, $id)
    {
        $indicador = IndIndicadores::findOrFail($id);
        $indicador->update([
            'nombre' => $request->name,
            'calculo' => $request->calculation,
            'meta' => $request->goal,
            'frecuencia' => $request->frecuencia,
            'responsable' => $request->responsible,
            'area' => $request->area,
            'consulta_bd' => $request->consultasql,
        ]);

        return redirect()->route('indicators.indicadores.index')->with('success', 'Indicador actualizado exitosamente.');
    }
}
