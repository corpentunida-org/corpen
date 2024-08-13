<?php

namespace App\Http\Controllers\Exequial;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ExMonitoria;
use App\Models\Parentescos;
use Illuminate\Support\Facades\Http;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;

class MaeC_ExSerController extends Controller
{
    public function index()
    {
        $registros = ExMonitoria::orderBy('id', 'desc')->get();
        $controllerparentesco = app()->make(ParentescosController::class);        
        foreach ($registros as $registro) {
            $nomPar = $controllerparentesco->showName($registro->parentesco);
            $registro->parentesco = $nomPar;
        }
        return view('exequial.prestarServicio.index', ['registros' => $registros]);
    }
    public function store(Request $request)
    {
        //$this->authorize('create', auth()->user());
        $controllerparentesco = app()->make(ParentescosController::class);
        $codPar = $controllerparentesco->show($request->parentesco);
        $fechaActual = Carbon::now();
        ExMonitoria::create([
            'fechaRegistro' => $fechaActual,
            'horaFallecimiento' => $request->horaFallecimiento,
            'cedulaTitular'  => $request->cedulaTitular,
            'nombreTitular' => $request->nameTitular,
            'nombreFallecido' => $request->nameBeneficiary,
            'cedulaFallecido'  => $request->cedulaFallecido,
            'fechaFallecimiento' => $request->fechaFallecimiento,
            'lugarFallecimiento' => $request->lugarFallecimiento,
            'parentesco'=> $codPar,
            'traslado'=> $request->traslado,
            'contacto'=> $request->contacto,
            'telefonoContacto'=> $request->telefonoContacto,
            'Contacto2'=> $request->contacto2,
            'telefonoContacto2'=> $request->telefonoContacto2,
            'factura'=> $request->factura,
            'valor'=> $request->valor,
        ]);

        //Cambio en beneficiarios de estado a false
        // $modelo = ComaeExRelPar::where('cedula', $request->cedulaFallecido)->first()->refresh();
        // $modelo->estado = '0';
        // $modelo->save();
        $token = env('TOKEN_ADMIN');
        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $token,
            'Accept' => '*/*',
        ])->put('https://www.siasoftapp.com:7011/api/Exequiales/Beneficiary', [
            "name" => $request->nameBeneficiary,
            "codeParentesco" => $codPar,
            "type" => "I",
            "dateEntry"=>$fechaActual,
            "documentBeneficiaryId" => $request->cedulaFallecido,
            "dateBirthDate"=>$request->fecNacFallecido,
        ]);
        if ($response->successful()) {
            return $this->index();
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
        ])->put('https://www.siasoftapp.com:7011/api/Exequiales/Beneficiary', [
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
