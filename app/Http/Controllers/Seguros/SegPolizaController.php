<?php

namespace App\Http\Controllers\Seguros;

use App\Http\Controllers\Controller;
use App\Models\Seguros\SegPoliza;
use App\Models\Seguros\SegBeneficiario;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

use App\Models\Seguros\SegAsegurado;

class SegPolizaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('seguros.polizas.index');
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
        $poliza = SegPoliza::where('seg_asegurado_id', $id)
            ->with(['tercero', 'asegurado', 'asegurado.terceroAF', 'plan.condicion', 'plan.coberturas'])->first();
        if (!$poliza){
            return redirect()->route('seguros.poliza.index')->with('warning', 'No se encontró la cédula como asegurado de una poliza');
        }
        $titularCedula = $poliza->asegurado->titular;
        $grupoFamiliar = SegAsegurado::where('Titular', $titularCedula)->with('tercero','polizas.plan.coberturas')->get();
        //dd($grupoFamiliar);
        $totalPrima = DB::table('SEG_polizas')
            ->whereIn('seg_asegurado_id', $grupoFamiliar->pluck('cedula'))
            ->sum('valor_prima');

        $beneficiarios = SegBeneficiario::where('id_asegurado', $id)->get();

        return view('seguros.polizas.show', compact('poliza','grupoFamiliar', 'totalPrima', 'beneficiarios'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(SegPoliza $segPoliza)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, SegPoliza $segPoliza)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(SegPoliza $segPoliza)
    {
        //
    }
}
