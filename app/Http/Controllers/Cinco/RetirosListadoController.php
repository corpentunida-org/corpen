<?php

namespace App\Http\Controllers\Cinco;

use App\Models\Cinco\RetirosListado;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Cinco\Terceros;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class RetirosListadoController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $tabla = DB::table('CIN_condicionesRetiros')->get();
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
        $tabla = DB::table('CIN_condicionesRetiros')->get();
        return view('cinco.retiros.create', compact('tabla'));
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
        $tercero = Terceros::where('Cod_Ter', $id)->first();
        if (!$tercero) {
            return redirect()->route('cinco.retiros.index')->with('error', 'No hay registros con esa cedula ingresada.');
        }
        $arrayliquidacion = $this->liquidaciones($tercero->Fec_Minis);
        return view('cinco.retiros.show', compact('tercero', 'arrayliquidacion'));
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
        $valorFijo = DB::table('CIN_condicionesRetiros')->where('anio', $i)->first();
        if ($fecha->year === $i) {            
            $difMes = 12 - $fecha->month;
            $difDia = 31 - $fecha->day;
            $calculo = (($difMes * $valorFijo->valor) / 12) + ((($difDia * $valorFijo->valor) / 12) / 30);
            return round($calculo);
        }else if ($fecha->year < 2006) {    
            if ($i == 2006){                 
                $difAnio = 2006 - $fecha->year;
                $difMes = 12 - $fecha->month;
                $difDia = 31 - $fecha->day;
                $calculo = ($difAnio * $valorFijo->valor) + (($difMes * $valorFijo->valor) / 12) + ((($difDia * $valorFijo->valor) / 12) / 30);
                return round($calculo);
            }  
            else{
                return $valorFijo->valor;
            } 
        } 
        else {
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
        $valorFijo = DB::table('CIN_condicionesRetiros')->where('anio', $i)->first();
        if ($valorFijo) {
            $plus = $this->calculoPlus($fecha, $i . '-12-31', $valorFijo->plus) ?? 0;
            if ($fecha->year === $i) {
                $difMes = 12 - $fecha->month;
                $difDia = 31 - $fecha->day;
                $liquidacion = (($difMes * $valorFijo->valor) / 12) + ((($difDia * $valorFijo->valor) / 12) / 30);
                return [$liquidacion, $plus];
            } else {
                return [$valorFijo->valor, $plus];
            }
        }
    }

    public function edit(RetirosListado $retirosListado)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, RetirosListado $retirosListado)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(RetirosListado $retirosListado)
    {
        //
    }
}
