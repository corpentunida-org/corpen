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
    private function auditoria($accion,$area){
        $auditoriaController = app(AuditoriaController::class);
        $auditoriaController->create($accion, $area);
    }
    public function index()
    {
        $registros = ExMonitoria::orderBy('id', 'desc')->get();
        $controllerparentesco = app()->make(ParentescosController::class);        
        foreach ($registros as $registro) {
            $nomPar = $controllerparentesco->showName($registro->parentesco);
            $registro->parentesco = $nomPar;
        }
        $mReg = ExMonitoria::whereMonth('fechaFallecimiento', Carbon::now()->month)->count();
        $nmen = ExMonitoria::where('genero', 'M')->count();
        $nwomen = ExMonitoria::where('genero', 'F')->count();
        
        return view('exequial.prestarServicio.index', compact('registros','mReg', 'nmen', 'nwomen'));
    }

    public function edit($id)
    {
        $registro = ExMonitoria::findOrFail($id);
        $controllerparentesco = app()->make(ParentescosController::class);   
        
        $nomPar = $controllerparentesco->showName($registro->parentesco);
        $registro->parentesco = $nomPar;
        
        return view('exequial.prestarServicio.edit', [
            'registro' => $registro
        ]);
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
                "observation"=> "PASTOR FALLECIDO" . $request->observation,
                "stade"=> false
            ]);
            ComaeExCli::where('cod_cli', $request->cedulaTitular)->update(['estado' => false]);
            $request->parentesco = "TITULAR";
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
            ComaeExRelPar::where('cod_cli', $request->cedula)->update([
                'estado' => false,
                'tipo' => 'I',
            ]);
        }
        
        if ($response->successful()) {
            ExMonitoria::create([
                'fechaRegistro' => $fechaActual,
                'horaFallecimiento' => $request->horaFallecimiento,
                'cedulaTitular'  => $request->cedulaTitular,
                'nombreTitular' => $request->nameTitular,
                'nombreFallecido' => strtoupper($request->nameBeneficiary),
                'cedulaFallecido'  => $request->cedulaFallecido,
                'fechaFallecimiento' => $request->fechaFallecimiento,
                'lugarFallecimiento' => strtoupper($request->lugarFallecimiento),
                'parentesco'=> $request->parentesco,
                'traslado'=> $request->traslado,
                'contacto'=> strtoupper($request->contacto),
                'telefonoContacto'=> $request->telefonoContacto,
                'Contacto2'=> strtoupper($request->contacto2),
                'telefonoContacto2'=> $request->telefonoContacto2,
                'factura'=> $request->factura,
                'valor'=> $request->valor,
                'genero'=>$request->genero,
            ]);
            $accion = "prestar servicio a " . $request->cedulaFallecido;
            $this->auditoria($accion, "EXEQUIALES");
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
        $registros = ExMonitoria::orderBy('id', 'desc')->get();
        $controllerparentesco = app()->make(ParentescosController::class);        
        foreach ($registros as $registro) {
            $nomPar = $controllerparentesco->showName($registro->parentesco);
            $registro->parentesco = $nomPar;
        }
        $pdf = Pdf::loadView('exequial.prestarServicio.indexpdf', 
            ['registros' => $registros,'image_path' => public_path('assets/images/CORPENTUNIDA_LOGO PRINCIPAL  (2).png'),])
                ->setPaper('letter', 'landscape');
        return $pdf->download(date('Y-m-d') . " Reporte.pdf");
        //return view('exequial.prestarServicio.indexpdf', ['registros' => $registros, 'image_path' => public_path('assets/images/CORPENTUNIDA_LOGO PRINCIPAL  (2).png')]);
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
            $accion = "actualizar prestar servicio a " . $request->cedulaFallecido;
            $this->auditoria($accion, "EXEQUIALES");
            return redirect()->route('exequial.prestarServicio.index')->with('success', 'Registro actualizado exitosamente');
        }else{
            return redirect()->route('exequial.prestarServicio.index')->with('error', 'Error al actualizar el registro');
            //return response()->json(['error' => 'Error al actualizar el registro'], 500);
        }
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
