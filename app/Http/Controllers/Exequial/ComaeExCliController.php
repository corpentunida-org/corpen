<?php

namespace App\Http\Controllers\Exequial;

use App\Http\Controllers\AuditoriaController;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Exequiales\ComaeExRelPar;
use App\Models\Exequiales\ComaeExCli;
use App\Models\Exequiales\ComaeTer;
use Illuminate\Support\Facades\Http;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;

class ComaeExCliController extends Controller
{
    /* public function __construct()
    {
        $this->middleware(['auth']);
    } */

    private function auditoria($accion){
        $auditoriaController = app(AuditoriaController::class);
        $auditoriaController->create($accion);
    }

    public function index()
    {
        return view('exequial.asociados.index');
    }

    //Datos solo del titular
    public function titularShow($id){
        //API
        $token = env('TOKEN_ADMIN');
        $titular = Http::withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->get(env('API_PRODUCCION') . '/api/Exequiales/Tercero', [
            'documentId' => $id,
        ]);
        if ($titular->successful()) {
            $jsonTit = $titular->json();
            return $jsonTit;
        } else {
            return redirect()->route('exequial.asociados.index')->with('messageTit', 'No se encontró la cédula como titular de exequiales');
        }
    }

    public function edit($id) {
        $jsonTit = $this->titularShow($id);
        $controllerplanes = app()->make(PlanController::class);
        return view('exequial.asociados.edit', [
            'asociado' => $jsonTit, 
            'plans' => $controllerplanes->index(),
        ]);
    }


    public function show(Request $request, $id)
    {      
        //cedula por url asociados/ID?id=
        //A la BD
        $id = $request->input('id');
        $asociado = ComaeExCli::where('cedula', $id)->firstOrFail();
        $beneficiarios = ComaeExRelPar::where('cedulaAsociado', $id)->with('parentescoo')->get();
        return view('exequial.beneficiarios.show', compact('asociado', 'beneficiarios'));
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
        //$this->authorize('create', auth()->user());
        $token = env('TOKEN_ADMIN');
        $fechaActual = Carbon::now();
        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $token,
            'Accept' => 'application/json',
        ])->post(env('API_PRODUCCION') . '/api/Exequiales/Tercero', [
            'documentId' => $request->documentId,
            'obsevations' => $request->observaciones,
            'dateStart' => $fechaActual,
            'descuento' => 0,
            'codePlan' => $request->plan,
            'codeCenterCost' => "C1010"
        ]);
        //dd($request->documentId);
        if ($response->successful()) {
            $accion = "add titular " . $request->documentId;
            $this->auditoria($accion);
            return response()->json([
                'redirect' => route('exequial.beneficiarios.show', ['beneficiario' => $request->documentId]),
                'message' => 'Titular añadido exitosamente.'
            ]);
        } else {
            $jsonResponse = $response->json();
            $message = $jsonResponse['message'];
            if ($message === "El pastor no se encuentra registrado, debe registrarlo para tener acceso al servicio de exequiales") {
                $code = 1;
            } elseif ($message === "Ya se encuetra registrado como titular de exequiales con esa cedula") {
                $code = 2;
            }
            else $code = 3;
            return response()->json(['error' => $message, 'code' => $code], $response->status());
        }
    }
    //actualizar fechas
    // public function update(Request $request, $cedula)
    // {
    //     ComaeExCli::where('cedula', $cedula)
    //         ->update(['fechaNacimiento' => $request->input('fechaNacimiento')]);

    //     $asociado = ComaeExCli::where('cedula', $cedula)->firstOrFail();
    //     $beneficiarios = ComaeExRelPar::where('cedulaAsociado', $cedula)->get();
    //     return view('exequial.asociados.show', compact('asociado', 'beneficiarios'))->with('success', 'Datos actualizados');
    // }

    public function update(Request $request){
        //$this->authorize('update', auth()->user());        
        $token = env('TOKEN_ADMIN');
        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $token,
            'Accept' => 'application/json',
        ])->patch(env('API_PRODUCCION') . '/api/Exequiales/Tercero', [
            'documentId' => $request->documentid,
            'dateInit' => $request->dateInit,
            'codePlan'=> $request->codePlan,
            'discount'=> $request->discount,
            'observation'=> $request->observation,
            'stade'=> true 
        ]);        
        if ($response->successful()) {
            $accion = "update titular " . $request->documentid;
            $this->auditoria($accion);
            //return $data; //antigua vista
            //plantilla            
            $url = route('exequial.beneficiarios.show', ['beneficiario' => 'ID']) . '?id=' . $request->documentid;
            return redirect()->to($url)->with('success', 'Titular actualizado exitosamente');
        } else {
            return response()->json(['error' => $response->json()], $response->status());
        }
      
    }
    

    public function generarpdf($id)
    {
        $token = env('TOKEN_ADMIN');

        $titular = Http::withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->get(env('API_PRODUCCION') . '/api/Exequiales/Tercero', [
            'documentId' => $id,
        ]);
        $beneficiarios = Http::withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->get(env('API_PRODUCCION') . '/api/Exequiales', [
            'documentId' => $id,
        ]);
        $personalTitular = Http::withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->get(env('API_PRODUCCION') . '/api/Pastors', [
            'documentId' => $id,
        ]);

        if ($titular->successful() && $beneficiarios->successful()) {
            $jsonTit = $titular->json();            
            $jsonBene = $beneficiarios->json();
            if (isset($jsonTit['codePlan'])) {
                $controllerplanes = app()->make(PlanController::class);
                $nomPlan = $controllerplanes->nomCodPlan($jsonTit['codePlan']);
                $jsonTit['codePlan'] = $nomPlan;
            }
            
            $data = [
                'asociado' => $jsonTit, 
                'beneficiarios' => $jsonBene,
                'pastor' => $personalTitular,
                'image_path' => public_path('assets/img/corpentunida-logo-azul-oscuro-2021x300.png'),
            ];
            
            // return view('exequial.asociados.showpdf', [
            //     'asociado' => $jsonTit, 
            //     'beneficiarios' => $jsonBene,
            //     'pastor' => $personalTitular,
            //     'image_path' => public_path('assets/img/corpentunida-logo-azul-oscuro-2021x300.png'),
            // ]);
            $pdf = Pdf::loadView('exequial.asociados.showpdf', $data)->setPaper('letter', 'landscape');
            return $pdf->download(date('Y-m-d') . " Reporte " .  $jsonTit['documentId'] . '.pdf');
            
        }
        //$asociado = ComaeExCli::where('cedula', $id)->with(['ciudade', 'distrito'])->firstOrFail();
        //$beneficiarios = ComaeExRelPar::where('cedulaAsociado', $id)->with('parentescoo')->get();     
        //$data = ['asociado'=> $asociado, 'beneficiarios' => $beneficiarios];
        //$pdf = PDF::loadView('asociados.showpdf', $data)->setPaper('legal', 'landscape');
        //return $pdf->download(date('Y-m-d') .  $asociado->nombre . '.pdf');
        //return view('asociados.showpdf', $data);
    }
}
