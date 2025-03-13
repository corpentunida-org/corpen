<?php

namespace App\Http\Controllers\Seguros;

use App\Http\Controllers\Controller;
use App\Models\Seguros\SegAsegurado;
use App\Models\Seguros\SegBeneficiario;
use App\Models\Seguros\Parentescos;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\AuditoriaController;

class SegBeneficiarioController extends Controller
{
    private function auditoria($accion){
        $auditoriaController = app(AuditoriaController::class);
        $auditoriaController->create($accion, "SEGUROS");
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request){
        //cedula nombre de titular
        $id = $request->input('a');
        $asegurado = SegAsegurado::with('tercero')->where('cedula', $id)->first();
        $parentescos = Parentescos::all();
        $poliza = $request->input('p');
        return view('seguros.beneficiarios.create', compact('asegurado','parentescos', 'poliza'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //dd($request);
        $beneficiario = SegBeneficiario::create([
            'tipo_documento_id' => $request->tipoDocumento,
            'cedula' => $request->cedula,
            'nombre' => strtoupper($request->names),
            'parentesco' => $request->parentesco,
            'porcentaje' => $request->porcentaje,
            'cedula_contingente' => $request->cedulaContingente,
            'nombre_contingente' => strtoupper($request->nombreContingente),
            'id_poliza' => $request->poliza,
            'id_asegurado' => $request->asegurado,
            'telefono' => $request->telefono,
            'correo' => $request->correo,
        ]);
        $url = route('seguros.poliza.show', ['poliza' => 'ID']) . '?id=' . $request->asegurado;
        if ($beneficiario) {
            $accion = "add beneficiario  " . $request->cedula;
            $this->auditoria($accion);    
            return redirect()->to($url)->with('success', 'Beneficiario agregado correctamente.');
        }else{
            return redirect()->to($url)->with('error', 'No se pudo agregar el beneficiario, intente mas tarde.');
        }
    }

    /**
     * Display the specified resource.
     */
    public function show($idAsegurado)
    {
        $beneficiarios = SegBeneficiario::where('id_asegurado', $idAsegurado)->get();
        return $beneficiarios;
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {            
        $beneficiario = SegBeneficiario::where('id', $id)->with(['asegurado.tercero'])->first();
        $parentescos = Parentescos::all();
        return view('seguros.beneficiarios.edit', compact('beneficiario', 'parentescos'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        //dd($request->aseguradoId);
        $beneficiario = SegBeneficiario::findOrFail($id);
        $beneficiario->update([
            'nombre' => strtoupper($request->input('names')),
            'parentesco' => $request->input('parentesco'),            
            'porcentaje' => $request->input('porcentaje'),            
            'telefono' => $request->input('telefono'),
            'correo' => $request->input('correo'),
        ]);
        $accion = "actualizar a " . $request->cedulaFallecido;
        $this->auditoria($accion);
        $url = route('seguros.poliza.show', ['poliza' => 'ID']) . '?id=' . $request->aseguradoId;
        return redirect()->to($url)->with('success', 'Beneficiario actualizado correctamente');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(SegBeneficiario $beneficiario)
    {
        SegBeneficiario::findOrFail($beneficiario->id)->delete();
        $this->auditoria("BENEFICIARIO ELIMINADO CEDULA" . $beneficiario->cedula);
        return redirect()->back()->with('success', 'Beneficiario eliminado correctamente.');
    }
}
