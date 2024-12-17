<?php

namespace App\Http\Controllers\Seguros;

use App\Http\Controllers\Controller;
use App\Models\Seguros\SegPoliza;
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
        
        $titularCedula = $poliza->asegurado->titular;
        $grupoFamiliar = SegAsegurado::where('Titular', $titularCedula)->with('tercero','polizas.plan.coberturas')->get();
        
        $totalPrima = DB::table('SEG_polizas')
        ->whereIn('seg_asegurado_id', $grupoFamiliar->pluck('cedula')) // IDs del grupo familiar
        ->sum('valor_prima');

        return view('seguros.polizas.show', compact('poliza','grupoFamiliar', 'totalPrima'));
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
