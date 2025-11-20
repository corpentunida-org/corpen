<?php

namespace App\Http\Controllers\Cinco;

use App\Models\Cinco\MoviContCinco;
use App\Models\Maestras\maeTerceros;
use App\Models\Cinco\Terceros;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Barryvdh\DomPDF\Facade\Pdf;

class MoviContCincoController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('cinco.movcontables.index');
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
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request)
    {
        $id = $request->input('id');

        $movimientos = MoviContCinco::where('Cedula', $id)
            ->with(['tercero', 'cuentaContable'])
            ->orderBy('Cuenta', 'asc')
            ->orderBy('id', 'asc')
            ->get();

        $ap = MoviContCinco::where('Cedula', $id)->where('Cuenta', '416542')->get();

        $meses = [
            'ENE' => 1,
            'FEB' => 2,
            'MAR' => 3,
            'ABR' => 4,
            'MAY' => 5,
            'JUN' => 6,
            'JUL' => 7,
            'AGO' => 8,
            'SEP' => 9,
            'OCT' => 10,
            'NOV' => 11,
            'DIC' => 12,
        ];

        $mesesInv = array_flip($meses);

        $faltantes = [];
        $ultimo = null;

        $apOrdenado = $ap->sortBy(function ($mov) use ($meses) {
            $obs = $mov->Observacion ?? '';
            if (preg_match('/(\d{4})-([A-Z]{3})/i', $obs, $m)) {
                $anio = intval($m[1]);
                $mes = $meses[strtoupper($m[2])] ?? 1;
                return $anio * 100 + $mes;
            }
            return 99999999;
        });

        try {
            foreach ($apOrdenado as $mov) {
                $obs = $mov->Observacion ?? '';
                if (!preg_match('/(\d{4})-([A-Z]{3})\s*a\s*(\d{4})-([A-Z]{3})/i', $obs, $match)) {
                    continue;
                }
                $anioInicio = intval($match[1]);
                $mesInicio = $meses[strtoupper($match[2])] ?? null;

                $anioFin = intval($match[3]);
                $mesFin = $meses[strtoupper($match[4])] ?? null;
                if (!$mesInicio || !$mesFin) {
                    continue;
                }
                if ($ultimo) {
                    $a = $ultimo['anio'];
                    $m = $ultimo['mes'] + 1;
                    $limite = 0;

                    if ($m > 12) {
                        $m = 1;
                        $a++;
                    }
                    while (!($a == $anioInicio && $m == $mesInicio)) {
                        if (!isset($mesesInv[$m])) {
                            break;
                        }
                        $faltantes[] = "AP. DE {$a}-" . $mesesInv[$m];
                        $m++;
                        if ($m > 12) {
                            $m = 1;
                            $a++;
                        }
                        $limite++;
                        if ($limite > 200) {
                            break;
                        }
                    }
                }

                $ultimo = ['anio' => $anioFin, 'mes' => $mesFin];
            }
        } catch (\Throwable $e) {
            $faltantes = [];
            $apOrdenado = $ap = MoviContCinco::where('Cedula', $id)->where('Cuenta', '416542')->get();
        }

        
        $cuentasAgrupadas = MoviContCinco::select('cuenta')->with('cuentaContable')->where('Cedula', $id)->groupBy('cuenta')->orderBy('cuenta')->get();

        
        $fechas = maeTerceros::select('nom_ter', 'fec_minis', 'fecha_ipuc', 'fec_aport')->where('Cod_Ter', $id)->first();

        $tercero = Terceros::where('cod_ter', $id)->first();

        if ($movimientos->isEmpty()) {
            return redirect()->back()->with('warning', 'No hay registros con esa cédula ingresada.');
        }

        return view('cinco.movcontables.show', compact('movimientos', 'cuentasAgrupadas', 'id', 'fechas', 'tercero', 'faltantes', 'apOrdenado'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(MoviContCinco $moviContCinco) {}

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, MoviContCinco $moviContCinco)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(MoviContCinco $moviContCinco)
    {
        //
    }

    public function generarpdf($id)
    {
        $movimientos = MoviContCinco::where('Cedula', $id)
            ->with(['tercero', 'cuentaContable'])
            ->orderBy('Cuenta', 'asc')
            ->orderBy('Fecha', 'asc')
            ->get();

        $cuentasAgrupadas = MoviContCinco::select('cuenta')->with('cuentaContable')->where('Cedula', $id)->groupBy('cuenta')->orderBy('cuenta')->get();

        $image_path = public_path('assets/images/CORPENTUNIDA_LOGO PRINCIPAL  (2).png');

        //return view('cinco.movcontables.reportepdf', compact('movimientos', 'cuentasAgrupadas', 'image_path'));
        $pdf = Pdf::loadView('cinco.movcontables.reportepdf', compact('movimientos', 'cuentasAgrupadas', 'id', 'image_path'))->setPaper('letter', 'landscape');
        return $pdf->download(date('Y-m-d') . ' Reporte ' . $id . '.pdf');
    }
}
