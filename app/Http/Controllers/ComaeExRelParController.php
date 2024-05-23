<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ComaeExRelPar;
use App\Models\ComaeExCli;
use Carbon\Carbon;

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
        ComaeExRelPar::create([
            'cedula' => $request->cedula,
            'cedulaAsociado' => $request->cedulaAsociado,
            'nombre' => $request->apellidos . ' ' . $request->nombres,
            'fechaNacimiento' => $request->fechaNacimiento,
            'fechaIngreso' => $fechaActual,
            'parentesco' => $request->parentesco
        ]);        
        return "se añadio el registro";       
    }
}
