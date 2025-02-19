<?php

namespace App\Http\Controllers\Seguros;

use App\Http\Controllers\Controller;
use App\Models\Seguros\SegPoliza;
use App\Models\Seguros\SegReclamaciones;
use Illuminate\Http\Request;
use App\Models\Seguros\SegAsegurado;
use App\Models\Seguros\SegEstadoReclamacion;
use App\Models\Seguros\SegCambioEstadoReclamacion;
use App\Http\Controllers\AuditoriaController;

class SegReclamacionesController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    private function auditoria($accion){
        $auditoriaController = app(AuditoriaController::class);
        $auditoriaController->create($accion, "SEGUROS");
    }
    public function index()
    {
        $reclamaciones = SegReclamaciones::with(['asegurado.terceroAF', 'asegurado.tercero', 'cobertura'])->get();
        return view("seguros.reclamaciones.index", compact('reclamaciones'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        $id = $request->query('a');
        $asegurado = SegAsegurado::where('cedula',$id)->with(['tercero','terceroAF'])->first();
        $poliza = SegPoliza::where('seg_asegurado_id', $id)->with(['plan.coberturas'])->first();
        $estados = SegEstadoReclamacion::all();
        return view("seguros.reclamaciones.create", compact('asegurado','poliza', 'estados'));
    }

    /**
     * Store a newly created resource in storage.
     */

    public function store(Request $request)
    {
        $reclamacion = SegReclamaciones::create([
            'cedulaAsegurado' => $request->asegurado,
            'idCobertura' => $request->cobertura_id,
            'idDiagnostico' => $request->diagnostico_id,
            'otro' => $request->otrodiagnostico,
            'fechaSiniestro' => $request->fechasiniestro,
            'fechaContacto' => $request->fechacontacto,
            'horaContacto' => $request->horacontacto,
            'nombreContacto' => strtoupper($request->nombrecontacto),
            'parentescoContacto' => $request->parentesco_id,
            'estado' => $request->estado_id,
            'poliza_id' => $request->poliza_id,
        ]);

        SegPoliza::where('id', $request->poliza_id)
            ->update(['reclamacion' => $request->estado_id]);
        
        $cambioEstado = SegCambioEstadoReclamacion::create([
            'reclamacion_id' => $reclamacion->id,
            'estado_id' => $request->estado_id,
            'observacion' => strtoupper($request->observacion),
            'fecha_actualizacion' => now()->toDateString(),
            'hora_actualizacion' => now()->toTimeString(),
        ]);

        $url = route('seguros.poliza.show', ['poliza' => 'ID']) . '?id=' . $request->asegurado;
        if ($reclamacion && $cambioEstado) {
            $accion = "add nueva reclamacion  " . $request->asegurado;
            $this->auditoria($accion);    
            return redirect()->to($url)->with('success', 'Proceso de reclamación añadido exitosamente.');
        }else{
            return redirect()->to($url)->with('error', 'No se pudo agregar la reclamación, intente mas tarde.');
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
