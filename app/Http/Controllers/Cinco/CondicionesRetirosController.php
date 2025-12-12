<?php

namespace App\Http\Controllers\Cinco;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\Maestras\maeTerceros;
use App\Models\Cinco\Retiros;

class CondicionesRetirosController extends Controller
{
    public function store(Request $request)
    {
        try {
            DB::table('RET_condicionesRetiros')->insert([
                'anio' => $request->anio,
                'valor' => $request->valor,
                'plus' => $request->valorplus ?? 0,
            ]);
            return redirect()->back()->with('success', 'Condición de retiro creada exitosamente.');
        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->with('error', 'Error al crear la condición de retiro: ' . $e->getMessage());
        }
    }

    public function create()
    {
        $opciones = DB::table('Ret_opciones')->where('activo', 1)->get()->groupBy('tipo');
        return view('cinco.parametros.create', compact('opciones'));
    }

    public function generarpdf($id)
    {
        $tercero = maeTerceros::with('distrito')->where('Cod_Ter', $id)->first();
        $retiro = Retiros::where('cod_ter', $id)->with('tipoRetiroNom')->first();
        $opciones = DB::table('Ret_opciones')->orderBy('tipo')->get()->groupBy('tipo');
        $ret_opciones = DB::table('RET_retiros_opciones')->where('cod_ter', $id)->get();
        $image_path = public_path('assets/images/CORPENTUNIDA_LOGO PRINCIPAL  (2).png');
        $valores = [];
        foreach ($ret_opciones as $r) {
            $valores[$r->id_opcion] = $r->valor;
        }
        return view('cinco.retiros.liquidacionpdf', compact('image_path', 'tercero', 'retiro', 'ret_opciones', 'opciones','valores'));
        /*$pdf = Pdf::loadView('cinco.retiros.liquidacionpdf', compact( 'image_path','tercero', 'retiro','ret_opciones', 'opciones','valores'))
            ->setPaper('letter', 'landscape');
        return $pdf->download(date('Y-m-d') . " Reporte " . $id . '.pdf');*/
    }
}
