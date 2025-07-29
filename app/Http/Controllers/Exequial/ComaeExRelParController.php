<?php

namespace App\Http\Controllers\Exequial;

use App\Http\Controllers\AuditoriaController;
use App\Http\Controllers\Exequial\ComaeExCliController;
use App\Http\Controllers\Controller;
use App\Models\Exequiales\ComaeExRelPar;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Http;

class ComaeExRelParController extends Controller
{
    private function auditoria($accion,$area){
        $auditoriaController = app(AuditoriaController::class);
        $auditoriaController->create($accion,$area);
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
        ComaeExRelPar::create([
            'cedula' => $request->documentid,
            'nombre' => strtoupper($request->apellidos . ' ' . $request->nombres),
            'cod_par' => $request->codePar,
            'tipo' => "A",
            'fec_ing' => $fechaActual,
            'fec_nac' => $request->fechaNacimiento,
            'estado' => true,
            'cod_cli' => $request->cedulaAsociado
        ]);
        if ($response->successful()) {
            $accion = "add beneficiario " . $request->documentid;
            $this->auditoria($accion, "EXEQUIALES");
            //return response()->json(['message' => 'Se agrego beneficiario correctamente', 'data' => $response->json()], $response->status());
            $url = route('exequial.asociados.show', ['asociado' => 'ID']) . '?id=' . $request->cedulaAsociado;
            return redirect()->to($url)->with('success', 'Beneficiario agregado exitosamente');
        } else {
            return redirect()->back()->with('error', $response->json() . $response->status());
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
        ComaeExRelPar::where('cedula', $request->cedula)->update([
            'nombre' => strtoupper($request->names),
            'cod_par' => $request->parentesco,
            'fec_nac' => $request->fechaNacimiento,
        ]);
        $url = route('exequial.asociados.show', ['asociado' => 'ID']) . '?id=' . $request->documentid;      
        if ($response->successful()) {
            $accion = "update beneficiario " . $request->cedula;
            $this->auditoria($accion, "EXEQUIALES");
            //return response()->json(['message' => 'Se actualizó correctamente', 'data' => $response->json()], $response->status());
            return redirect()->to($url)->with('success', 'Beneficiario actualizado exitosamente');
        } else {
            return redirect()->to($url)->with('msjerror', 'No se pudo actualizar el titular ' . $response->json());
        }
    }
    
    public function destroy(Request $request)
    {
        //$this->authorize('delete', auth()->user());
        $id = $request->id;
        $token = env('TOKEN_ADMIN');
        $response = Http::withHeaders([
            'Accept' => '*/*',
            'Authorization' => 'Bearer ' . $token,
        ])->delete(env('API_PRODUCCION') . '/api/Exequiales/Beneficiary?idUser=' . $id);
        ComaeExRelPar::where('cedula', $request->cedula)->update([
            'estado' => false,
        ]);
        $url = route('exequial.asociados.show', ['asociado' => 'ID']) . '?id=' . $request->documentid;
        if ($response->successful()) {
            $accion = "delete beneficiario " . $request->beneid;
            $this->auditoria($accion, "EXEQUIALES");
            //return $response->status();
            return redirect()->to($url)->with('success', 'Beneficiario eliminado exitosamente');
        } else {
            //return response()->json(['error' => $response->json()], $response->status());
            return redirect()->to($url)->with('msjerror', 'No se pudo eliminar el titular ' . $response->json());
        }
    }

}
