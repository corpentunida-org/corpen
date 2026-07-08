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
        foreach ($indicators as $grupo) {
            foreach ($grupo as $ind) {
                if (!empty($ind->consulta_bd)) {
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
