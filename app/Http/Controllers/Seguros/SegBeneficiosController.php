<?php

namespace App\Http\Controllers\Seguros;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Models\Seguros\SegAsegurado;
use App\Models\Seguros\SegBeneficios;
use App\Models\Seguros\SegPoliza;
use App\Models\Seguros\SegPlan;
use App\Http\Controllers\AuditoriaController;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\ExcelExport;


class SegBeneficiosController extends Controller
{
    /**
     * Display a listing of the resource.
     */

    private function auditoria($accion)
    {
        $auditoriaController = app(AuditoriaController::class);
        $auditoriaController->create($accion, 'SEGUROS');
    }

    public function index()
    {
        $beneficios = SegBeneficios::with(['tercero', 'polizarel'])->where('active', true)->get();
        $planes = SegPlan::where('vigente', true)->where('condicion_id', 2)->get();
        return view('seguros.beneficios.index', compact('planes', 'beneficios'));
    }

    public function listFilter(Request $request)
    {
        DB::enableQueryLog();
        $query = SegPoliza::with(['asegurado', 'tercero', 'plan']);
        $fechaMax = Carbon::now()->subYears($request->edad_minima)->endOfDay();
        $fechaMin = Carbon::now()->subYears($request->edad_maxima)->startOfDay();

        $query->whereHas('tercero', function ($q) use ($fechaMin, $fechaMax) {
            $q->whereBetween('fec_nac', [$fechaMin, $fechaMax]);
        });

        $tipoAsegurado = $request->tipo;
        if ($tipoAsegurado === 'VIUDA') {
            $query->whereHas('asegurado', function ($q) {
                $q->where('viuda', true);
            });
        } else if ($tipoAsegurado != 'TODOS') {
            $query->whereHas('asegurado', function ($q) use ($tipoAsegurado) {
                $q->where('parentesco', $tipoAsegurado);
            });
        }

        if ($request->has('planes')) {
            $valoresPlanes = array_map('intval', $request->planes);
            $query->whereIn('valor_asegurado', $valoresPlanes)->get();
        }
        $listadata = $query->get();
        $planes = SegPlan::where('vigente', true)
            ->where('condicion_id', 2)
            ->get();

        return view('seguros.beneficios.index', compact('listadata', 'planes'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        if ($request->grupo) {
            if (filter_var($request->checkconfirmarbene)) {
                foreach ($request->beneficio as $item) {
                    $poliza = SegPoliza::where('id', $item['poliza'])->first();
                    SegBeneficios::create([
                        'cedulaAsegurado' => $item['cedula'],
                        'poliza' => $item['poliza'],
                        'porcentajeDescuento' => $request->despor,
                        'valorDescuento' => $request->desval,
                        'observaciones' => strtoupper($request->observaciones),
                        'valorpagaranterior' => $poliza->primapagar
                    ]);
                    $poliza->update([
                        'primapagar' => floatval($poliza->primapagar) - floatval($request->desval),
                    ]);
                    $asegurado = SegAsegurado::where('cedula', $item['cedula'])->first();
                    if (filter_var($asegurado->viuda)) {
                        SegPoliza::where('seg_asegurado_id', $item['cedula'])->update([
                            'valorpagaraseguradora' => floatval($poliza->valorpagaraseguradora) - floatval($request->desval),
                        ]);
                    } else {
                        SegPoliza::where('seg_asegurado_id', $asegurado->titular)->update([
                            'valorpagaraseguradora' => floatval($poliza->valorpagaraseguradora) - floatval($request->desval),
                        ]);
                    }
                }
            } else {
                foreach ($request->beneficio as $item) {
                    SegBeneficios::create([
                        'cedulaAsegurado' => $item['cedula'],
                        'poliza' => $item['poliza'],
                        'porcentajeDescuento' => $request->despor,
                        'valorDescuento' => $request->desval,
                        'observaciones' => strtoupper($request->observaciones),
                    ]);
                }
            }
            $accion = 'add beneficio grupal valor de ' . $request->desval;
            $this->auditoria($accion);
            return redirect()->route('seguros.beneficios.index')->with('success', 'Se agrego correctamente el beneficio.');
        } else {
            $valorAnterior = (int) $request->valorbene + (int) $request->valorPrima;
            $beneficio = SegBeneficios::create([
                'cedulaAsegurado' => $request->aseguradoId,
                'poliza' => $request->polizaId,
                'porcentajeDescuento' => $request->porbene,
                'valorDescuento' => $request->valorbene,
                'valorpagaranterior' => $valorAnterior,
                'observaciones' => strtoupper($request->observacionesbene),
            ]);
            if ($request->boolean('checkconfirmarbene')) {
                $asegurado = SegAsegurado::where('cedula', $request->aseguradoId)->first();
                $poliza = SegPoliza::where('seg_asegurado_id', $asegurado->titular)->first();
                $poliza->update([
                    'valorpagaraseguradora' => floatval($poliza->valorpagaraseguradora) - floatval($request->valorbene),
                ]);
                SegPoliza::where('seg_asegurado_id', $request->aseguradoId)->update([
                    'primapagar' => $request->valorPrima
                ]);
            }
            if ($beneficio) {
                $accion = 'add beneficio a ' . $request->aseguradoId . ' por valor de ' . $request->valorbene;
                $this->auditoria($accion);
                return redirect()->back()->with('success', 'Se agrego correctamente el beneficio.');
            }
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(SegBeneficios $SegBeneficios)
    {

    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $SegBeneficios = SegBeneficios::findOrFail($id);
        $poliza = SegPoliza::with(['tercero'])->findOrFail($SegBeneficios->poliza);
        return view('seguros.beneficios.edit', compact('SegBeneficios', 'poliza'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, SegBeneficios $beneficio)
    {
        if ($request->checkconfirmarbene) {
            $update = SegBeneficios::find($beneficio->id);
            $anteriorbeneficio = $update->valorDescuento;
            $update->update([
                'valorDescuento' => $request->desval,
                'porcentajeDescuento' => $request->porval,
            ]);
            SegPoliza::where('id', $beneficio->poliza)->update([
                'primapagar' => $request->valorPrima,
            ]);
            $difbeneficio = floatval($request->desval) - floatval( $anteriorbeneficio);
            $polizatitular = SegPoliza::where('seg_asegurado_id', $request->titular)->first();
            $polizatitular->update([
                'valorpagaraseguradora' => floatval($polizatitular->valorpagaraseguradora) - $difbeneficio,
            ]);
            $this->auditoria("Actualizar beneficio id " . $beneficio->id . " con el valor de " . $request->desval);
            return redirect()->route('seguros.beneficios.index')->with('success', 'Se actualizo correctamente el beneficio.');
        }
        return redirect()->route('seguros.beneficios.index')->with('error', 'No se actualizó el beneficio, debe confirmar el cambio de valores.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id, Request $request)
    {
        $beneficio = SegBeneficios::find($id);
        if ($request->opcdestroy == 'individual') {
            $titular = SegAsegurado::where('cedula', $beneficio->cedulaAsegurado)->value('titular');
            $beneficio->update([
                'active' => false,
                'fechaFin' => Carbon::now()->toDateTimeString(),
            ]);
            $poliza = SegPoliza::where('id', $beneficio->poliza)->first();
            $poliza->update([
                'primapagar' => $beneficio->valorpagaranterior,
            ]);
            $titularpol = SegPoliza::where('seg_asegurado_id', $titular)->first();
            $titularpol->update([
                'valorpagaraseguradora' => $titularpol->valorpagaraseguradora + $beneficio->valorDescuento,
            ]);
            $this->auditoria("Eliminar beneficio a " . $beneficio->cedulaAsegurado);
            return redirect()->back()->with('success', 'Se elimino correctamente el beneficio.');
        } elseif ($request->opcdestroy == 'grupo') {
            $grupo = SegBeneficios::where('observaciones', $beneficio->observaciones)->get();
            $cantupd = 0;
            foreach ($grupo as $item) {
                $titular = SegAsegurado::where('cedula', $beneficio->cedulaAsegurado)->value('titular');
                $upd = $item->update([
                    'active' => false,
                    'fechaFin' => Carbon::now()->toDateTimeString(),
                ]);
                $poliza = SegPoliza::where('id', $item->poliza)->update([
                    'primapagar' => $item->valorpagaranterior,
                ]);
                $titularpol = SegPoliza::where('seg_asegurado_id', $titular)->first();
                $titularpol->update([
                    'valorpagaraseguradora' => $titularpol->valorpagaraseguradora + $beneficio->valorDescuento,
                ]);
                if ($upd) {
                    $cantupd++;
                }
            }
            $this->auditoria("Eliminar beneficio grupal " . $cantupd . " registros. Observacion: " . $beneficio->observaciones);
            return redirect()->back()->with('success', 'Se elimino correctamente el beneficio a ' . $cantupd . ' asegurados.');
        }
    }

    public function exportFiltroPdf(Request $request)
    {
        $registros = json_decode($request->listadata, true);
        $pdf = Pdf::loadView('seguros.beneficios.filtropdf', ['registros' => $registros, 'image_path' => public_path('assets/images/CORPENTUNIDA_LOGO PRINCIPAL  (2).png')])->setPaper('letter', 'landscape');

        return $pdf->download(date('Y-m-d') . ' Lista Polizas.pdf');
        //return view('seguros.beneficios.filtropdf', ['registros' => $registros, 'image_path' => public_path('assets/images/CORPENTUNIDA_LOGO PRINCIPAL  (2).png')]);
    }

    public function exportexcel(Request $request)
    {
        $datos = json_decode($request->listadata, true);
        $headings = ['N°', 'Cedula Asegurado', 'Nombre Asegurado', 'Parentesco', 'Edad', 'Plan', 'Valor a Pagar'];
        $datosFormateados = collect($datos)->map(function ($item, $index) {
            $edad = Carbon::parse($item['tercero']['fec_nac'])->age;
            return [
                'N°' => $index + 1,
                'CÉDULA ASEGURADO' => $item['seg_asegurado_id'] ?? '',
                'NOMBRE ASEGURADO' => $item['tercero']['nom_ter'] ?? '',
                'PARENTESCO' => $item['asegurado']['parentesco'] ?? '',
                'EDAD' => $edad,
                'PLAN' => $item['plan']['name'] ?? '',
                'VALOR ASEGURADO' => is_numeric($item['valor_asegurado']) ? number_format((float) $item['valor_asegurado']) : '',
                'VALOR A PAGAR' => is_numeric($item['primapagar']) ? number_format((float) $item['primapagar']) : '',
            ]
            ;
        });
        return Excel::download(new ExcelExport($datosFormateados, $headings), date('Y-m-d') . ' Lista Polizas.xlsx');
    }
}
