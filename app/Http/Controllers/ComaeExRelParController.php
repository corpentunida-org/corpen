<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ComaeExRelPar;
use App\Models\ComaeExCli;
use Carbon\Carbon;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ComaeExRelParController extends Controller
{
    public function update(Request $request, $cedula)
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
    }

    public function store(Request $request)
    {
        $request->validate([
            'cedula' => ['required', 'min:3', 'unique:comaeexrelpar,cedulaAsociado'],
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

        $token = 'eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJ1bmlxdWVfbmFtZSI6ImFkbWluQGdtYWlsLmNvbSIsImp0aSI6ImZmZGRjYWNhLTlmZTItNDdkZi04NGMyLTNhODhmNjExNzE3NyIsImh0dHA6Ly9zY2hlbWFzLnhtbHNvYXAub3JnL3dzLzIwMDUvMDUvaWRlbnRpdHkvY2xhaW1zL25hbWUiOiJBZG1pbiIsIlVzZXJJZCI6IjEiLCJtYWlsIjoiYWRtaW5AZ21haWwuY29tIiwiVXNlcnJvbGUiOiJBZG1pbiIsImh0dHA6Ly9zY2hlbWFzLm1pY3Jvc29mdC5jb20vd3MvMjAwOC8wNi9pZGVudGl0eS9jbGFpbXMvcm9sZSI6IkFkbWluIiwiZXhwIjoxNzIwODk3ODk1LCJpc3MiOiJteWFwcCIsImF1ZCI6Im15YXBwIn0.JqmbabE0ysqvyIsbHlBItze6f3p12nVLc3f-RlbCeaQ'; 

        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $token,
            'Accept' => 'application/json', // Ajusta esto según el tipo de datos que esperas recibir
        ])->post('https://www.siasoftapp.com:7011/api/Exequiales/Beneficiary', [
            'documentBeneficiaryId' => $request->cedula,
            'codePastor' => $request->cedulaAsociado,
            'name' => $request->apellidos . ' ' . $request->nombres,
            'dateBirthDate' => $request->fechaNacimiento,
            'dateEntry' => $fechaActual,
            'codeParentesco' => $request->parentesco,
            'type' => "A"
        ]);
    
        // Manejo de la respuesta de la API
        if ($response->successful()) {
            // Éxito
            return $response->json(); // O devuelve la vista que corresponda
        } else {
            // Manejo de errores
            return $response->json(); // O devuelve la vista que corresponda con el mensaje de error
        }
    }
}
