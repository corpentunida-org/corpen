<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ComaeExRelPar;
use Illuminate\Support\Facades\Http;
use App\Models\ExMonitoria;
use App\Models\Parentescos;
use Carbon\Carbon;

class ExMonitoriaController extends Controller
{
    public function index()
    {
        $registros = ExMonitoria::all();
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
            'type' => "I",
            "name" => $request->nameBeneficiary,
            "codeParentesco" => $codPar,
            'documentBeneficiaryId' => $request->cedulaFallecido,
        ]);
        if ($response->successful()) {
            return $this->index();
        }
    }

    public function ConsultaMes($mes)
    {
        $parentescos = ExMonitoria::whereMonth('fechaFallecimiento', $mes)->get();
        return response()->json($parentescos);
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
        return view('exequial.prestarServicio.indexpdf', ['registros' => $registros]);
    }
}
