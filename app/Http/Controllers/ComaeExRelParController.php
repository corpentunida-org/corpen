<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ComaeExRelPar;
use App\Models\ComaeExCli;
use Carbon\Carbon;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\PlanController;

class ComaeExRelParController extends Controller
{   

    public function show(Request $request, $id){
        //API
        $token = env('TOKEN_ADMIN');
        $id = $request->input('id');
        $titular = Http::withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->get('https://www.siasoftapp.com:7011/api/Exequiales/Tercero', [
            'documentId' => $id,
        ]);
        // $titular = Http::withHeaders([
        //     'Authorization' => 'Bearer ' . $token,
        // ])->get('https://www.siasoftapp.com:7011/api/Pastors', [
        //     'documentId' => $id,
        // ]);
        $beneficiarios = Http::withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->get('https://www.siasoftapp.com:7011/api/Exequiales', [
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
            return view('exequial.beneficiarios.show', [
                'asociado' => $jsonTit, 
                'beneficiarios' => $jsonBene,
            ]);
        } else {
            return redirect()->route('asociados.index')->with('messageTit', 'No se encontró la cédula como titular de exequiales');
        }
    }

    //update BD fechas de nacimiento
    /* public function update(Request $request, $cedula)
    {
        $ids = $request->input('ids');
        $fechas = $request->input('fechas');

        foreach ($ids as $key => $id) {
            $beneficiario = ComaeExRelPar::find($id);
            if ($beneficiario) {
                $beneficiario->fechaNacimiento = $fechas[$key];
                $beneficiario->save();
            }
        }
        $asociado = ComaeExCli::where('cedula', $cedula)->firstOrFail();
        $beneficiarios = ComaeExRelPar::where('cedulaAsociado', $cedula)->get();
        return view('asociados.show', compact('asociado', 'beneficiarios'))->with('success', 'Datos actualizados');
    } */
    public function update(Request $request)
    {
        //$this->authorize('update', auth()->user());

        $token = env('TOKEN_ADMIN');
        $fechaActual = Carbon::now();
        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $token,
            'Accept' => 'application/json',
        ])->put('https://www.siasoftapp.com:7011/api/Exequiales/Beneficiary', [
            'name' => $request->names,
            'codeParentesco' => $request->parentesco,
            'type' => "A",
            'dateEntry' => $fechaActual,
            'documentBeneficiaryId' => $request->cedula,
            'dateBirthDate' => $request->fechaNacimiento,
        ]);
        if ($response->successful()) {
            // Procesar la respuesta exitosa
            return response()->json(['message' => 'Se actualizó correctamente', 'data' => $response->json()], $response->status());
        } else {
            // Manejar errores si la solicitud no fue exitosa
            return response()->json(['error' => $response->json()], $response->status());
        }
    }

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
        // ComaeExRelPar::create([
        //     'cedula' => $request->cedula,
        //     'cedulaAsociado' => $request->cedulaAsociado,
        //     'nombre' => $request->apellidos . ' ' . $request->nombres,
        //     'fechaNacimiento' => $request->fechaNacimiento,
        //     'fechaIngreso' => $fechaActual,
        //     'parentesco' => $request->parentesco
        // ]);
        //return "se añadio el registro"; //Interno Controlador

        $token = env('TOKEN_SIASOFT');

        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $token,
            'Accept' => 'application/json',
        ])->post('https://www.siasoftapp.com:7011/api/Exequiales/Beneficiary', [
            'documentBeneficiaryId' => $request->cedula,
            'codePastor' => $request->cedulaAsociado,
            'name' => $request->apellidos . ' ' . $request->nombres,
            'dateBirthDate' => $request->fechaNacimiento,
            'dateEntry' => $fechaActual,
            'codeParentesco' => $request->parentesco,
            'type' => "A"
        ]);    
        return $response->json();
    }

    public function destroy($id)
    {
        //$this->authorize('delete', auth()->user());

        $token = env('TOKEN_ADMIN');
        $url = 'https://www.siasoftapp.com:7011/api/Exequiales/Beneficiary?documentId=' . $id;
        $response = Http::withHeaders([
            'Accept' => '*/*',
            'Authorization' => 'Bearer ' . $token,
        ])->delete($url);

        return $response->status();
    }

    

}
