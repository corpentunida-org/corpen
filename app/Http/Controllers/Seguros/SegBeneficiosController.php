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
use Carbon\Carbon;


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
        $beneficios = SegBeneficios::with(['tercero', 'polizarel'])->get();
        //dd($beneficios);
        $planes = SegPlan::where('vigente', true)->where('condicion_id', 2)->get();
        return view('seguros.beneficios.index', compact('planes', 'beneficios'));
    }

    public function listFilter(Request $request)
    {
        $query = SegPoliza::with(['asegurado', 'tercero', 'plan']);
        $fechaMax = Carbon::now()->subYears($request->edad_minima)->endOfDay(); 
        $fechaMin = Carbon::now()->subYears($request->edad_maxima)->startOfDay(); 

        $query->whereHas('tercero', function ($q) use ($fechaMin, $fechaMax) {
            $q->whereBetween('fechaNacimiento', [$fechaMin, $fechaMax]);
        });

        $tipoAsegurado = $request->tipo;
        if ($tipoAsegurado === 'VIUDA') {
            $query->whereHas('asegurado', function ($q) {
                $q->where('viuda', true);
            });
        } else {
            $query->whereHas('asegurado', function ($q) use ($tipoAsegurado) {
                $q->where('parentesco', $tipoAsegurado);
            });
        }

        if ($request->has('planes')) {
            $valoresPlanes = $request->planes;
            $query->whereHas('plan', function ($q) use ($valoresPlanes) {
                $q->where('vigente', true)
                    ->whereIn('valor', $valoresPlanes);
            });
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
            if(filter_var($request->checkconfirmarbene)){
                foreach ($request->beneficio as $item) {
                    $poliza = SegPoliza::where('id', $item['poliza'])->first();
                    SegBeneficios::create([
                        'cedulaAsegurado' => $item['cedula'],
                        'poliza' => $item['poliza'],
                        'porcentajeDescuento' => $request->despor,
                        'valorDescuento' => $request->desval,
                        'observaciones' => strtoupper($request->observaciones),
                        'valorpagaranterior'=> $poliza->primapagar
                    ]);
                    $poliza->update([
                        'primapagar' => floatval($poliza->primapagar) - floatval($request->desval),
                    ]);
                    $asegurado = SegAsegurado::where('cedula', $item['cedula'])->first();
                    if (filter_var($asegurado->viuda)){
                        SegPoliza::where('seg_asegurado_id', $item['cedula'])->update([
                            'valorpagaraseguradora' => floatval($poliza->valorpagaraseguradora) - floatval($request->desval),
                        ]);
                    }else{
                        SegPoliza::where('seg_asegurado_id', $asegurado->titular)->update([
                            'valorpagaraseguradora' => floatval($poliza->valorpagaraseguradora) - floatval($request->desval),
                        ]);
                    }
                }
            }
            else{
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
    public function edit(SegBeneficios $SegBeneficios)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, SegBeneficios $SegBeneficios)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(SegBeneficios $SegBeneficios)
    {
        //
    }
}
