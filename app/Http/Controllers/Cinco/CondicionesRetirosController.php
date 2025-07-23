<?php

namespace App\Http\Controllers\Cinco;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;

class CondicionesRetirosController extends Controller
{
    public function store(Request $request)
    {
        try {
            DB::table('CIN_condicionesRetiros')->insert([
                'anio' => $request->anio,
                'valor' => $request->valor,
                'plus' => $request->valorplus ?? 0,
            ]);
            return redirect()->back()->with('success', 'Condición de retiro creada exitosamente.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error al crear la condición de retiro: ' . $e->getMessage());
        }
    }

    public function generarpdf($id)
    {
        $image_path = public_path('assets/images/CORPENTUNIDA_LOGO PRINCIPAL  (2).png');
        return view('cinco.retiros.liquidacionpdf', compact( 'image_path'));
        /* $pdf = Pdf::loadView('cinco.retiros.liquidacionpdf', compact( 'image_path'))
            ->setPaper('letter', 'portrait');
        return $pdf->download(date('Y-m-d') . " Reporte " . $id . '.pdf'); */
    }

}
