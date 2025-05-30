<?php

namespace App\Http\Controllers\Cinco;

use App\Models\Cinco\RetirosListado;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Cinco\Terceros;
use Carbon\Carbon;

class RetirosListadoController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('cinco.retiros.index');
    }

    public function namesearch(Request $request)
    {
        $name = str_replace(' ', '%', $request->input('id'));
        $terceros = Terceros::whereHas('tercero', function ($query) use ($name) {
                $query->where('Nom_Ter', 'like', '%' . $name . '%');})->get();
        return view('seguros.polizas.search', compact('asegurados'));
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
        $tercero = Terceros::where('Cod_Ter', $id)->first();
        //dd($tercero->Fec_Minis);
        //$arrayliquidacion = $this->liquidaciones($tercero->Fec_Minis);
        return view('cinco.retiros.show', compact('tercero'));
    }

    private function liquidaciones(string $fechaminis)
    {
        $arrayliquidacion = [];
        $fecha = Carbon::parse($fechaminis);
        $dia = $fecha->day;
        $mes = $fecha->month;
        $año = $fecha->year;
        dd($dia, $mes, $año);
        return $arrayliquidacion;
    }
    /* private double anio2007(int cod_ter)
        {
            double calculo = 0;
            var r = retAnioMesDia(cod_ter);
            int valorFijo = 576000;
            if (r.Item1 < 2007)
            {
                calculo = valorFijo;
                return calculo;
            }
            else if (r.Item1 == 2007)
            {
                int difMes = 12 - r.Item2;
                int difDia = 31 - r.Item3;

                calculo = ((difMes * valorFijo) / 12) + (((difDia * valorFijo) / 12) / 30);
                return (int)Math.Ceiling(calculo);
            }
            else return calculo;
        } */
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
