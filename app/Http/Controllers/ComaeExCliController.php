<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ComaeExRelPar;
use App\Models\ComaeExCli;
use Dompdf\Dompdf;
use Illuminate\Support\Facades\View;
use Barryvdh\DomPDF\Facade\Pdf;

class ComaeExCliController extends Controller
{
    /* public function __construct()
    {
        $this->middleware(['auth']);
    } */ 

    public function index()
    {
        //$asociados = ComaeExCli::all();
        $asociados = ComaeExCli::with(['ciudade', 'distrito'])->paginate(10);
        return view('asociados.index', ['asociados' => $asociados]);
    }

    public function show(Request $request, $id)
    {
        //cedula por url asociados/ID?id=
        $id = $request->input('id');
        $asociado = ComaeExCli::where('cedula', $id)->firstOrFail();
        $beneficiarios = ComaeExRelPar::where('cedulaAsociado', $id)->with('parentescoo')->get();
        return view('asociados.show', compact('asociado', 'beneficiarios'));
    }

    public function update(Request $request, $cedula)
    {
        ComaeExCli::where('cedula', $cedula)
            ->update(['fechaNacimiento' => $request->input('fechaNacimiento')]);

        $asociado = ComaeExCli::where('cedula', $cedula)->firstOrFail();
        $beneficiarios = ComaeExRelPar::where('cedulaAsociado', $cedula)->get();
        return view('asociados.show', compact('asociado', 'beneficiarios'))->with('success', 'Datos actualizados');
    }

    public function generarpdf($id)
    {
        $asociado = ComaeExCli::where('cedula', $id)->with(['ciudade', 'distrito'])->firstOrFail();
        $beneficiarios = ComaeExRelPar::where('cedulaAsociado', $id)->with('parentescoo')->get();     
        $data = ['asociado'=> $asociado, 'beneficiarios' => $beneficiarios];
        //$pdf = PDF::loadView('asociados.showpdf', $data)->setPaper('legal', 'landscape');
        //return $pdf->download(date('Y-m-d') .  $asociado->nombre . '.pdf');
        return view('asociados.showpdf', $data);
    }
}
