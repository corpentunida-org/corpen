<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ComaeExRelPar;
use App\Models\ExMonitoria;
use App\Models\Parentescos;
use Carbon\Carbon;

class ExMonitoriaController extends Controller
{
    public function index()
    {
        $registros = ExMonitoria::with(['asociado', 'beneficiario', 'parentescoo'])->get();
        return view('monitoria.index', ['registros' => $registros]);
    }
    public function store(Request $request)
    {
        $fechaActual = Carbon::now();

        ExMonitoria::create([
            'fechaRegistro' => $fechaActual,
            'horaFallecimiento' => $request->horaFallecimiento,
            'cedulaTitular'  => $request->cedulaTitular,
            'cedulaFallecido'  => $request->cedulaFallecido,
            'fechaFallecimiento' => $request->fechaFallecimiento,
            'lugarFallecimiento' => $request->lugarFallecimiento,
            'parentesco'=> Parentescos::where('nomPar', $request->parentesco)->value('codPar'),
            'traslado'=> $request->traslado,
            'contacto'=> $request->contacto,
            'telefonoContacto'=> $request->telefonoContacto,
            'Contacto2'=> $request->contacto2,
            'telefonoContacto2'=> $request->telefonoContacto2,
            'factura'=> $request->factura,
            'valor'=> $request->valor,
        ]);

        //Cambio en beneficiarios de estado a false
        $modelo = ComaeExRelPar::where('cedula', $request->cedulaFallecido)->first()->refresh();
        $modelo->estado = '0';
        $modelo->save();

        return redirect()->back()->with('PrestarServicio', 'Registro exitoso');
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
        $registros = ExMonitoria::with(['asociado', 'beneficiario', 'parentescoo'])->get();
        return view('monitoria.indexpdf', ['registros' => $registros]);    
    }
}
