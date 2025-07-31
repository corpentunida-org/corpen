<?php

namespace App\Http\Controllers\Cinco;

use App\Models\Cinco\MoviContCinco;
use App\Models\Maestras\maeTerceros;
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
            ->orderBy('Fecha', 'asc')
            ->get();

        $cuentasAgrupadas = MoviContCinco::select('cuenta')
            ->with('cuentaContable')
            ->where('Cedula', $id)
            ->groupBy('cuenta')
            ->orderBy('cuenta')
            ->get();

        $fechas = maeTerceros::select('nom_ter','fec_minis', 'fecha_ipuc', 'fec_aport')->where('Cod_Ter', $id)->first();
        if ($movimientos->isEmpty()) {
            return redirect()->back()->with('warning', 'No hay registros con esa cedula ingresada.');
        }
        return view('cinco.movcontables.show', compact('movimientos', 'cuentasAgrupadas', 'id', 'fechas'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(MoviContCinco $moviContCinco)
    {

    }

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
            ->orderBy('Cuenta', 'asc')->orderBy('Fecha', 'asc')->get();

        $cuentasAgrupadas = MoviContCinco::select('cuenta')
            ->with('cuentaContable')->where('Cedula', $id)
            ->groupBy('cuenta')->orderBy('cuenta')->get();

        $image_path = public_path('assets/images/CORPENTUNIDA_LOGO PRINCIPAL  (2).png');
        
        //return view('cinco.movcontables.reportepdf', compact('movimientos', 'cuentasAgrupadas', 'image_path'));
        $pdf = Pdf::loadView('cinco.movcontables.reportepdf', compact('movimientos', 'cuentasAgrupadas', 'id' ,'image_path'))
        ->setPaper('letter', 'landscape');
                return $pdf->download(date('Y-m-d') . " Reporte " . $id . '.pdf');
    }
}
