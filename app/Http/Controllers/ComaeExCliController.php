<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ComaeExRelPar;
use App\Models\ComaeExCli;
use App\Models\ComaeTer;
use Illuminate\Support\Facades\Http;
use Carbon\Carbon;
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
        //A la BD
        $id = $request->input('id');
        $asociado = ComaeExCli::where('cedula', $id)->firstOrFail();
        $beneficiarios = ComaeExRelPar::where('cedulaAsociado', $id)->with('parentescoo')->get();
        return view('asociados.show', compact('asociado', 'beneficiarios'));
    }

    public function validarRegistro(Request $request){
        $id = $request->input('id');
        $asociado = ComaeExCli::where('cedula', $id)->first();
        if($asociado) return "1";        
        else{
            $tercero = ComaeTer::where('cod-ter', $id)->first();
            if ($tercero) return '2';
            else return '0';
        }
    }


    public function store(Request $request)
    {
        $token = env('TOKEN_ADMIN');
        $fechaActual = Carbon::now();
        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $token,
            'Accept' => 'application/json',
        ])->post('https://www.siasoftapp.com:7011/api/Exequiales/Tercero', [
            'documentId' => $request->documentId,
            'name' => $request->name,
            'agreement' => 0,
            'dateEntry' => $fechaActual,
            'stade' => true,
            'obsevations' => $request->observaciones,
            'dateStart' => $fechaActual,
            'dateEnd' => $fechaActual,
            'descuento' => 0,
            'valuePlan' => 0,
            'codePlan' => $request->plan,
            'codeCenterCost' => "C1010"
        ]);
    
        if ($response->successful()) {
            return redirect()->route('beneficiarios.show', ['beneficiario' => $documentId])
            ->with('messageTit', 'Titular añadido exitosamente.');
        } else {
            // Manejar errores si la solicitud no fue exitosa
            return response()->json(['error' => $response->json()], $response->status());
        }
    }

    //actualizar fechas
    // public function update(Request $request, $cedula)
    // {
    //     ComaeExCli::where('cedula', $cedula)
    //         ->update(['fechaNacimiento' => $request->input('fechaNacimiento')]);

    //     $asociado = ComaeExCli::where('cedula', $cedula)->firstOrFail();
    //     $beneficiarios = ComaeExRelPar::where('cedulaAsociado', $cedula)->get();
    //     return view('asociados.show', compact('asociado', 'beneficiarios'))->with('success', 'Datos actualizados');
    // }
    public function update(Request $request){
        $data = $request->json()->all();
        $token = env('TOKEN_ADMIN');
        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $token,
            'Accept' => 'application/json',
        ])->patch('https://www.siasoftapp.com:7011/api/Exequiales/Tercero', [
            'documentId' => $data['documentid'],
            'dateInit' => $data['dateInit'],
            'codePlan'=> $data['codePlan'],
            'discount'=> $data['discount'],
            'observation'=> $data['observation']
        ]);
    
        return $data;
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
