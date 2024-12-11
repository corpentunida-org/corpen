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

    public function edit($id,Request $request){
        $idTit = $request->input('asociadoid');       
        $beneficiario = [
            'name' => $request->input('name'),
            'relationship' => $request->input('relationship'),
            'dateBirthday' => substr($request->input('dateBirthday'), 0, 10)
        ];
        $controllerTit = app()->make(ComaeExCliController::class);
        $controllerparen = app()->make(ParentescosController::class);
        return view('exequial.beneficiarios.edit', [
            'id' => $id,
            'asociado' => $controllerTit->titularShow($idTit),
            'parentescos' => $controllerparen->index(),
            'beneficiario' => $beneficiario
        ]);
    }
    
    public function create(Request $request){
        //cedula nombre de titular
        $asociado = $request->input('asociado');
        $controllerTit = app()->make(ComaeExCliController::class);
        $controllerparen = app()->make(ParentescosController::class);
        //datos del titular
        $jsonTitular = $controllerTit->titularShow($asociado);
        //dd($asociado);
        return view('exequial.beneficiarios.create',[
            'parentescos' => $controllerparen->index(),
            'asociado' => $jsonTitular,
        ]);
    }
    
   public function store(Request $request)
   {        
        $fechaActual = Carbon::now();            
        $token = env('TOKEN_ADMIN');
        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $token,
            'Accept' => 'application/json',
            ])->post(env('API_PRODUCCION') . '/api/Exequiales/Beneficiary', [
                'documentBeneficiaryId' => $request->documentid,
                'codePastor' => $request->cedulaAsociado,
                'name' => $request->apellidos . ' ' . $request->nombres,
                'dateBirthDate' => $request->fechaNacimiento,
                'dateEntry' => $fechaActual,
                'codeParentesco' => $request->codePar,
                'type' => "A"
            ]);
        if ($response->successful()) {
            $accion = "add beneficiario " . $request->documentid;
            $this->auditoria($accion);
            //return response()->json(['message' => 'Se agrego beneficiario correctamente', 'data' => $response->json()], $response->status());
            $url = route('exequial.asociados.show', ['asociado' => 'ID']) . '?id=' . $request->cedulaAsociado;
            return redirect()->to($url)->with('success', 'Beneficiario agregado exitosamente');
        } else {
            return redirect()->back()->with('error', "aaaaa " . $response->json() . $response->status());
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
        $url = route('exequial.asociados.show', ['asociado' => 'ID']) . '?id=' . $request->documentid;      
        if ($response->successful()) {
            $accion = "update beneficiario " . $request->cedula;
            $this->auditoria($accion);
            //return response()->json(['message' => 'Se actualizó correctamente', 'data' => $response->json()], $response->status());
            $url = route('exequial.asociados.show', ['asociado' => 'ID']) . '?id=' . $request->documentid;
            return redirect()->to($url)->with('success', 'Beneficiario actualizado exitosamente');
        } else {
            return redirect()->to($url)->with('msjerror', 'No se pudo actualizar el titular ' . $response->json());
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
