<?php

namespace App\Http\Controllers\Exequial;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Exequiales\ExMonitoria;
use App\Models\Exequiales\Parentescos;
use Illuminate\Support\Facades\Http;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use App\Http\Controllers\AuditoriaController;

class MaeC_ExSerController extends Controller
{
    private function auditoria($accion){
        $auditoriaController = app(AuditoriaController::class);
        $auditoriaController->create($accion);
    }
    public function index()
    {
        $registros = ExMonitoria::orderBy('id', 'desc')->get();
        $controllerparentesco = app()->make(ParentescosController::class);        
        foreach ($registros as $registro) {
            $nomPar = $controllerparentesco->showName($registro->parentesco);
            $registro->parentesco = $nomPar;
        }
        /* $tReg = ExMonitoria::count();
        $mReg = ExMonitoria::whereMonth('fechaFallecimiento', Carbon::now()->month)->count(); */
        //return view('exequial.prestarServicio.index', ['registros' => $registros, 'totalRegistros'=>$tReg, 'mesRegistros'=>$mReg]);
        return view('exequial.prestarServicio.index', ['registros' => $registros]);
    }
    
    public function store(Request $request)
    {
        //$this->authorize('create', auth()->user());
        $token = env('TOKEN_ADMIN');
        $fechaActual = Carbon::now();
        if($request->pastor === 'true'){
            $codPar = null;
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $token,
                'Accept' => '*/*',
            ])->patch(env('API_PRODUCCION') . '/api/Exequiales/Tercero', [
                "documentId"=> $request->cedulaTitular,
                "dateInit"=> $request->dateInit ? $request->dateInit : ' ',
                "codePlan" => $request->codePlan ? $request->codePlan : '01',
                "discount"=> $request->discount,
                "observation"=> $request->observation,
                "stade"=> false
            ]);
            // return redirect()->back()
            //                  ->with('error', 'EN EL IF del titular');
        }else{
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $token,
                'Accept' => '*/*',
            ])->put(env('API_PRODUCCION') . '/api/Exequiales/Beneficiary', [
                "name" => $request->nameBeneficiary,
                "codeParentesco" => $request->parentesco,
                "type" => "I",
                "dateEntry"=>$request->dateInit ? $request->dateInit : ' ',
                "documentBeneficiaryId" => $request->cedulaFallecido,
                "dateBirthDate"=>$request->fecNacFallecido,
            ]);
        }
        if ($response->successful()) {
            ExMonitoria::create([
                'fechaRegistro' => $fechaActual,
                'horaFallecimiento' => $request->horaFallecimiento,
                'cedulaTitular'  => $request->cedulaTitular,
                'nombreTitular' => $request->nameTitular,
                'nombreFallecido' => $request->nameBeneficiary,
                'cedulaFallecido'  => $request->cedulaFallecido,
                'fechaFallecimiento' => $request->fechaFallecimiento,
                'lugarFallecimiento' => $request->lugarFallecimiento,
                'parentesco'=> $request->parentesco,
                'traslado'=> $request->traslado,
                'contacto'=> $request->contacto,
                'telefonoContacto'=> $request->telefonoContacto,
                'Contacto2'=> $request->contacto2,
                'telefonoContacto2'=> $request->telefonoContacto2,
                'factura'=> $request->factura,
                'valor'=> $request->valor,
            ]);
            $accion = "prestar servicio a " . $request->cedulaFallecido;
            $this->auditoria($accion);
            return $this->index();
        }
        else{
            return "error " . response()->json(['error' => $response->json()], $response->status());;
        }
    } 

    public function consultaMes($mes)
    {
        $filtromes = ExMonitoria::whereMonth('fechaFallecimiento', $mes)->get();
        return view('exequial.prestarServicio.index', ['registros' => $filtromes]);
    }

    /* public function exportData()
    {
        return Excel::download(new DataExport, 'data.xlsx');
    } */

    public function generarpdf(){
        $registros = ExMonitoria::all();
        $controllerparentesco = app()->make(ParentescosController::class);        
        foreach ($registros as $registro) {
            $nomPar = $controllerparentesco->showName($registro->parentesco);
            $registro->parentesco = $nomPar;
        }
        $pdf = Pdf::loadView('exequial.prestarServicio.indexpdf', 
            ['registros' => $registros,'image_path' => public_path('assets/img/corpentunida-logo-azul-oscuro-2021x300.png'),])
                ->setPaper('letter', 'landscape');
        return $pdf->download(date('Y-m-d') . " Reporte.pdf");
        //return view('exequial.prestarServicio.indexpdf', ['registros' => $registros]);

    }

    public function reporteIndividual($id){
        $registro = ExMonitoria::find($id);
        $controllerparentesco = app()->make(ParentescosController::class);        
        
            $nomPar = $controllerparentesco->showName($registro->parentesco);
            $registro->parentesco = $nomPar;
        
        $pdf = Pdf::loadView('exequial.prestarServicio.indexpdf', 
            ['registros' => collect([$registro]),'image_path' => public_path('assets/img/corpentunida-logo-azul-oscuro-2021x300.png'),])
                ->setPaper('letter', 'landscape');
        return $pdf->download(date('Y-m-d') . " " . $registro->cedulaFallecido . " Reporte.pdf");

        // return view('exequial.prestarServicio.indexpdf', [
        //     'registros' => collect([$registro]),
        //     'image_path' => public_path('assets/img/corpentunida-logo-azul-oscuro-2021x300.png'),]);
    
    }

    public function update(Request $request, $id){
        $updated  = ExMonitoria::where('id', $id)->update([
            'horaFallecimiento' => $request->horaFallecimiento,
            'fechaFallecimiento' => $request->fechaFallecimiento,
            'lugarFallecimiento' => $request->lugarFallecimiento,
            'traslado' => $request->traslado,
            'contacto' => $request->contacto,
            'telefonoContacto' =>$request->telefonoContacto,
            'Contacto2' =>$request->contacto2,
            'telefonoContacto2' =>$request->telefonoContacto2,
            'factura' =>$request->factura,
            'valor' =>$request->valor
        ]);
        if($updated > 0){
            return response()->json(['message' => 'Actualización exitosa']);
        }else{
            return response()->json(['error' => 'Error al actualizar el registro'], 500);
        }

        // $reg = ExMonitoria::find($id);
        // $reg->horaFallecimiento = $request->horaFallecimiento;
        // $reg->fechaFallecimiento = $request->fechaFallecimiento;
        // $reg->lugarFallecimiento = $request->lugarFallecimiento;
        // $reg->traslado = $request->traslado;
        // $reg->contacto = $request->contacto;
        // $reg->telefonoContacto = $request->telefonoContacto;
        // $reg->Contacto2 = $request->Contacto2;
        // $reg->telefonoContacto2 = $request->telefonoContacto2;
        // $reg->factura = $request->factura;
        // $reg->valor = $request->valor;
        // $reg->save();
        // return response()->json(['message' => 'Actualización exitosa']);
    }

    public function destroy($id){
        $token = env('TOKEN_ADMIN');
        $fechaActual = now()->toDateString();
        $nombreFallecido = ExMonitoria::find($id)->value('nombreFallecido');
        $parentesco = ExMonitoria::find($id)->value('parentesco');
        $cedulaFallecido = ExMonitoria::find($id)->value('cedulaFallecido');
        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $token,
            'Accept' => 'application/json',
        ])->put(env('API_PRODUCCION') . '/api/Exequiales/Beneficiary', [
            'name' => $nombreFallecido,
            'codeParentesco' => $parentesco,
            'type' => "A",
            'dateEntry' => $fechaActual,
            'documentBeneficiaryId' => $cedulaFallecido,
            'dateBirthDate' => $fechaActual,
        ]);
        if ($response->successful()) {
            //$deleted  = ExMonitoria::where('id', $id)->delete();
            // if($deleted > 0){
            //     return response()->json(['message' => 'Eliminacion exitosa']);
            // }
            // return response()->json(['message' => $response]);
            return response()->json([
                'status' => $response->status(),
                'headers' => $response->headers(),
                'body' => $response->body(),
                'json' => $response->json(),
                'successful' => $response->successful(),
                'serverError' => $response->serverError(),
                'clientError' => $response->clientError(),
            ]);
        }else{
            return response()->json(['error' => 'error al cambiar estado',], 500);
        }
    }
}
