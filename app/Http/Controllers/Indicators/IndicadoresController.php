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

        // Calculamos el promedio usando el método refactorizado
        $promedioAlcanzados = $this->calcularPromedio($indicators);

        return view('indicators.index', compact('indicators', 'lastReport', 'promedioAlcanzados'));
    }

    public function dataIndicadores()
    {
        $indicators = IndIndicadores::with('arearel')->get();

        foreach ($indicators as $ind) {
            $ind->calculo = (string) $ind->calculo;
            $ind->meta = (string) $ind->meta;
            $ind->frecuencia = $ind->frecuencia ?? 'Mensual';

            // AQUÍ EXTRAEMOS LA UNIDAD INTELIGENTEMENTE
            $metaData = $this->parseMeta($ind->meta);
            $ind->unidad_medida = $metaData['unidad'] ?? ''; // Ej: '%', 'horas', '/ 5', 'días'

            if (!empty($ind->consulta_bd)) {
                try {
                    $resultado = collect(DB::select($ind->consulta_bd))->first();
                    $ind->indicador_calculado = $resultado ? (float) array_values((array) $resultado)[0] : null;
                } catch (\Exception $e) {
                    throw new \Exception('Error en el indicador "' . $ind->nombre . '" - ' . $e->getMessage());
                }
            } else {
                $ind->indicador_calculado = null;
            }
        }

        return $indicators->groupBy(function($item) {
            return $item->arearel->nombre ?? 'Sin Área Asignada';
        });
    }

    private function calcularPromedio($indicatorsGrouped)
    {
        $totalIndicadores = 0;
        $indicadoresAlcanzados = 0;

        foreach ($indicatorsGrouped as $grupo) {
            foreach ($grupo as $ind) {
                // OMITIR TEMPORALMENTE LOS INDICADORES DE TIEMPO ERRÓNEOS (#6, #7, #8)
                if (in_array($ind->id, [6, 7, 8])) {
                    continue;
                }

                if ($ind->indicador_calculado !== null) {
                    $totalIndicadores++;

                    $meta = $this->parseMeta($ind->meta);

                    if (!empty($meta) && isset($meta['valor'])) {
                        $cumple = false;
                        if ($meta['op'] === '<=') {
                            $cumple = ($ind->indicador_calculado <= $meta['valor']);
                        } else {
                            $cumple = ($ind->indicador_calculado >= $meta['valor']);
                        }

                        if ($cumple) {
                            $indicadoresAlcanzados++;
                        }
                    }
                }
            }
        }

        $promedioAlcanzados = $totalIndicadores > 0 ? ($indicadoresAlcanzados / $totalIndicadores) * 100 : 0;
        return round($promedioAlcanzados, 2);
    }

    /**
     * Método unificado y mejorado para interpretar la Meta (Texto a Lógica Matemática)
     */
    private function parseMeta(?string $meta): array
    {
        if (empty($meta)) {
            return [];
        }

        $metaStr = trim(mb_strtolower($meta));

        // 1. Determinar el operador lógico por defecto (>=) o si es menor o igual (<=)
        $op = '>=';
        if (str_contains($metaStr, '<=') || str_contains($metaStr, '≤') || str_contains($metaStr, '<')) {
            $op = '<=';
        }

        // 2. Extraer el primer valor numérico de la cadena
        $valor = 0;
        if (preg_match('/(\d+(\.\d+)?)/', $metaStr, $m)) {
            $valor = (float) $m[1];
        }

        // 3. Determinar la unidad de medida visual
        $unidad = '';
        if (str_contains($metaStr, '%')) {
            $unidad = '%';
        } elseif (preg_match('/\/\s*5/', $metaStr)) {
            $unidad = '/ 5';
        } elseif (str_contains($metaStr, 'hora')) {
            $unidad = 'horas';
        } elseif (str_contains($metaStr, 'día') || str_contains($metaStr, 'dia')) {
            $unidad = 'días';
        }

        return [
            'op' => $op,
            'valor' => $valor,
            'unidad' => $unidad
        ];
    }

    public function show($id)
    {
        $indicator = IndIndicadores::findOrFail($id);
        return view('indicators.show', compact('indicator'));
    }

    public function descargarInforme()
    {
        try {
            // 1. Obtenemos los indicadores agrupados y procesados
            $indicators = $this->dataIndicadores();

            // 2. Calculamos el promedio general de cumplimiento
            $promedioAlcanzados = $this->calcularPromedio($indicators);

            // 3. Generamos el PDF
            $pdf = Pdf::loadView('indicators.informepdf', compact('indicators', 'promedioAlcanzados'))
                ->setPaper('A4', 'portrait');

            $fileName = 'InformeTIC_' . now()->format('Ymd_His') . '.pdf';

            // Guardamos en S3
            Storage::disk('s3')->put('corpentunida/indicators/' . $fileName, $pdf->output());

            IndRegistroInformes::create([
                'archivo' => 'corpentunida/indicators/' . $fileName,
                'usuario' => Auth::id(),
                'fecha_descarga' => now(),
            ]);

            return $pdf->download('informe_indicadores.pdf');

        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Ocurrió un error al generar el PDF: ' . $e->getMessage());
        }
    }

    public function create()
    {
        $areas = GdoArea::select('id', 'nombre')->get();
        $responsables = GdoEmpleado::selectRaw(
            "id, CONCAT(nombre1, ' ', IFNULL(nombre2, ''), ' ', apellido1, ' ', IFNULL(apellido2, '')) as nombre"
        )->where('id', '>=', 11)->get();

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
        $responsables = GdoEmpleado::selectRaw(
            "id, CONCAT(nombre1, ' ', IFNULL(nombre2, ''), ' ', apellido1, ' ', IFNULL(apellido2, '')) as nombre"
        )->where('id', '>=', 11)->get();

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
