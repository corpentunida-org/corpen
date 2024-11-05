<?php

namespace App\Http\Controllers\Exequial;

use App\Http\Controllers\AuditoriaController;
use App\Http\Controllers\Exequial\ComaeExCliController;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Http;

class ComaeExRelParController extends Controller
{
    private function auditoria($accion){
        $auditoriaController = app(AuditoriaController::class);
        $auditoriaController->create($accion);
    }


    public function edit($id,  Request $request){
        $idTit = $request->query('asociadoid');
        $controllerTit = app()->make(ComaeExCliController::class);
        return view('exequial.beneficiarios.edit', [
            'id' => $id,
            'asociado' => $controllerTit->titularShow($idTit),
        ]);
    }
    /* public function edit($idBene) {
        $controllerTit = app()->make(ComaeExCliController::class);
        return view('exequial.asociados.edit', [
            'asociado' => $controllerTit->titularShow(),
            'beneficiario' => ,
        ]);
    } */

    
   public function store(Request $request)
   {
       //$this->authorize('create', auth()->user());       
       $request->validate([
           'cedula' => ['required', 'min:3'],
           'fechaNacimiento' => ['required'],
           'apellidos' => ['required'],
           'nombres' => ['required'],
        ]);
        
        $fechaActual = Carbon::now();            
        $token = env('TOKEN_ADMIN');
        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $token,
            'Accept' => 'application/json',
            ])->post(env('API_PRODUCCION') . '/api/Exequiales/Beneficiary', [
                'documentBeneficiaryId' => $request->cedula,
                'codePastor' => $request->cedulaAsociado,
                'name' => $request->apellidos . ' ' . $request->nombres,
                'dateBirthDate' => $request->fechaNacimiento,
                'dateEntry' => $fechaActual,
                'codeParentesco' => $request->parentesco,
                'type' => "A"
            ]);
        if ($response->successful()) {
            $accion = "add beneficiario " . $request->cedula;
            $this->auditoria($accion);
            return response()->json(['message' => 'Se agrego beneficiario correctamente', 'data' => $response->json()], $response->status());
        } else {
            return response()->json(['error' => $response->json()], $response->status());
        }
    }
        
    public function update(Request $request)
    {
        //$this->authorize('update', auth()->user());
        $token = env('TOKEN_ADMIN');
        $fechaActual = Carbon::now();
        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $token,
            'Accept' => 'application/json',
        ])->put(env('API_PRODUCCION') . '/api/Exequiales/Beneficiary', [
            'name' => $request->names,
            'codeParentesco' => $request->parentesco,
            'type' => "A",
            'dateEntry' => $fechaActual,
            'documentBeneficiaryId' => $request->cedula,
            'dateBirthDate' => $request->fechaNacimiento,
        ]);
        if ($response->successful()) {
            $accion = "update beneficiario " . $request->cedula;
            $this->auditoria($accion);
            return response()->json(['message' => 'Se actualizó correctamente', 'data' => $response->json()], $response->status());
        } else {
            return response()->json(['error' => $response->json()], $response->status());
        }        
    }
    
    public function destroy($id)
    {
        //$this->authorize('delete', auth()->user());
        $token = env('TOKEN_ADMIN');
        $url = env('API_PRODUCCION') . '/api/Exequiales/Beneficiary?idUser=' . $id;
        $response = Http::withHeaders([
            'Accept' => '*/*',
            'Authorization' => 'Bearer ' . $token,
        ])->delete($url);
        if ($response->successful()) {
            $accion = "delete beneficiario " . $id;
            $this->auditoria($accion);
            return $response->status();
        } else {
            return response()->json(['error' => $response->json()], $response->status());
        }
    }

}
