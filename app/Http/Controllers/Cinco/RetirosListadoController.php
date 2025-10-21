<?php

namespace App\Http\Controllers\Cinco;

use App\Models\Cinco\Retiros;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Cinco\Terceros;
use App\Models\Maestras\maeTerceros;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class RetirosListadoController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $tabla = DB::table('RET_condicionesRetiros')->get();
        return view('cinco.retiros.index', compact('tabla'));
    }

    public function namesearch(Request $request)
    {
        $name = str_replace(' ', '%', $request->input('id'));
        $terceros = Terceros::where('Nom_Ter', 'like', '%' . $name . '%')->get();
        return view('cinco.movcontables.search', compact('terceros'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $tabla = DB::table('RET_condicionesRetiros')->get();
        return view('cinco.retiros.create', compact('tabla'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $añoActual = Carbon::now()->year;
        $ultimo = Retiros::where('consecutivoDocumento', 'like', "BPA-{$añoActual}%")->orderBy('consecutivoDocumento', 'desc')->first();
        if ($ultimo) {
            $numeroAnterior = (int) substr($ultimo->consecutivoDocumento, -3);
            $nuevoConsecutivo = str_pad($numeroAnterior + 1, 3, '0', STR_PAD_LEFT);
        } else {
            $nuevoConsecutivo = '001';
        }
        $totalbeneficios = floatval($request->beneficiovalor) - floatval($request->retencionvalor) + floatval($request->saldosvalor);
        $retiro = Retiros::create([
            'cod_ter' => $request->cod_ter,
            'tipoRetiro' => $request->TipoRetiro,
            'observación' => $request->observación,
            'fecInicialLiquidacion' => $request->fechaInicialLiquidacion,
            'consecutivoDocumento' => "BPA-{$añoActual}{$nuevoConsecutivo}",
            'beneficioAntiguedad' => $totalbeneficios,
        ]);

        foreach ($request->opcion as $opcionId => $valor) {
            if (!empty($valor) && floatval($valor) != 0) {
                DB::table('RET_retiros_opciones')->insert([
                    'cod_ter' => $request->cod_ter,
                    'documento' => $retiro->consecutivoDocumento,
                    'id_opcion' => $opcionId,
                    'valor' => $valor
                ]);
            }
        }
        return redirect()->route('cinco.retiros.show', ['calculoretiro' => 'ID','id' => $retiro->cod_ter,])->with('success', 'Retiro registrado correctamente.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request)
    {
        $id = $request->input('id');
        $tercero = maeTerceros::where('Cod_Ter', $id)->first();
        if (!$tercero) {
            return redirect()->route('cinco.retiros.index')->with('error', 'No hay registros con esa cedula ingresada.');
        }
        $opciones = DB::table('Ret_opciones')->where('activo', true)->get()->groupBy('tipo');
        $arrayliquidacion = $this->liquidaciones($tercero->fec_minis);
        $tiposselect = DB::table('RET_TiposRetiros')->where('activo', 1)->get();
        $retiro = Retiros::where('cod_ter', $id)->get();
        return view('cinco.retiros.show', compact('tercero', 'arrayliquidacion', 'tiposselect', 'opciones', 'retiro'));
    }

    private function liquidaciones(string $fechaminis)
    {
        $arrayliquidacion = [];
        $fecha = Carbon::parse($fechaminis);
        $anioActual = Carbon::now()->year;
        if ($fecha->year < 2006) {
            $anioInicio = 2006;
        } else {
            $anioInicio = $fecha->year;
        }
        for ($i = $anioInicio; $i <= $anioActual; $i++) {
            if ($i < 2017) {
                $arrayliquidacion[$i] = $this->antes2017($fecha, $i);
            } else {
                $resultado = $this->despues2017($fecha, $i);
                if (!is_null($resultado)) {
                    $arrayliquidacion[$i] = $resultado;
                }
            }
        }
        return $arrayliquidacion;
    }

    private function antes2017($fecha, $i)
    {
        $valorFijo = DB::table('RET_condicionesRetiros')->where('anio', $i)->first();
        if ($fecha->year === $i) {
            $difMes = 12 - $fecha->month;
            $difDia = 31 - $fecha->day;
            $calculo = ($difMes * $valorFijo->valor) / 12 + ($difDia * $valorFijo->valor) / 12 / 30;
            return round($calculo);
        } elseif ($fecha->year < 2006) {
            if ($i == 2006) {
                $difAnio = 2006 - $fecha->year;
                $difMes = 12 - $fecha->month;
                $difDia = 31 - $fecha->day;
                $calculo = $difAnio * $valorFijo->valor + ($difMes * $valorFijo->valor) / 12 + ($difDia * $valorFijo->valor) / 12 / 30;
                return round($calculo);
            } else {
                return $valorFijo->valor;
            }
        } else {
            return $valorFijo->valor;
        }
    }
    private function calculoPlus($fecha, $i, $plusVal)
    {
        if (Carbon::now()->year - $fecha->year > 5) {
            $fechaFin = Carbon::parse($i);
            $diferencia = $fecha->diffInDays($fechaFin);
            return ($diferencia / (365 / 12)) * $plusVal;
        } else {
            return 0;
        }
    }
    private function despues2017($fecha, $i)
    {
        $valorFijo = DB::table('RET_condicionesRetiros')->where('anio', $i)->first();
        if ($valorFijo) {
            $plus = $this->calculoPlus($fecha, $i . '-12-31', $valorFijo->plus) ?? 0;
            if ($fecha->year === $i) {
                $difMes = 12 - $fecha->month;
                $difDia = 31 - $fecha->day;
                $liquidacion = ($difMes * $valorFijo->valor) / 12 + ($difDia * $valorFijo->valor) / 12 / 30;
                return [$liquidacion, $plus];
            } else {
                return [$valorFijo->valor, $plus];
            }
        }
    }

    public function edit(RetirosListado $retiros)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Retiros $retiros)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Retiros $retiros)
    {
        //
    }
}
