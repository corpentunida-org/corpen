<?php

namespace App\Http\Controllers\Seguros;

use App\Http\Controllers\Controller;
use App\Models\Seguros\SegPoliza;
use App\Models\Seguros\SegReclamaciones;
use App\Models\Seguros\SegAsegurado;
use App\Models\Seguros\SegCobertura;
use App\Models\Seguros\SegEstadoReclamacion;
use App\Models\Seguros\SegCambioEstadoReclamacion;
use App\Http\Controllers\AuditoriaController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;


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
        $coberturas = SegCobertura::select('seg_coberturas.*', DB::raw('COUNT(SEG_reclamaciones.idCobertura) as reclamaciones_count'))
            ->leftJoin('SEG_reclamaciones', 'seg_coberturas.id', '=', 'SEG_reclamaciones.idCobertura')
            ->groupBy('seg_coberturas.id') // Asegúrate de agrupar por la columna que usas para la cobertura
            ->orderByDesc('reclamaciones_count')
            ->first();
        return view("seguros.reclamaciones.index", compact('reclamaciones','coberturas'));
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
            'cedulaContacto'=>$request->cedulacontacto,
            'nombreContacto' => strtoupper($request->nombrecontacto),
            'parentescoContacto' => $request->parentesco_id,
            'estado' => $request->estado_id,
            'poliza_id' => $request->poliza_id,
        ]);

        SegPoliza::where('id', $request->poliza_id)
            ->update(['reclamacion' => $reclamacion->id]);
        
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
    public function edit(SegReclamaciones $reclamacion)
    {        
        $asegurado = SegAsegurado::where('cedula',$reclamacion->cedulaAsegurado)->with(['tercero','terceroAF'])->first();
        $poliza = SegPoliza::where('seg_asegurado_id', $reclamacion->cedulaAsegurado)->with(['plan.coberturas'])->first();
        $estados = SegEstadoReclamacion::all();
        return view("seguros.reclamaciones.edit", compact('reclamacion','asegurado','poliza', 'estados'));
    }
    
    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //cambiar el estado de la reclamacion
        $update = SegReclamaciones::where('id', $id)->update([
            'estado' => $request->estado_id,
        ]);

        //crear registro cambio de estado de la reclamacion
        $crear = SegCambioEstadoReclamacion::create([
            'estado_id' => $request->estado_id,
            'reclamacion_id'=>$id,
            'observacion' => strtoupper($request->observacion),
            'fecha_actualizacion' => now()->toDateString(),
            'hora_actualizacion' => now()->toTimeString(),
        ]);

        /* //actualizar poliza
        $poliza = SegPoliza::where('id', $request->poliza_id)->update([
            'reclamacion' => $request->estado_id,
        ]); */

        if ($update && $crear) {
            $accion = "update reclamacion id " . $id;
            $this->auditoria($accion);    
            return redirect()->route('seguros.reclamacion.index')->with('success', 'Reclamación actualizada exitosamente.');
        }else{
            return redirect()->route('seguros.reclamacion.index')->with('error', 'No se pudo actualizar la reclamación, intente mas tarde.');
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }

    public function generarpdf(Request $request){
        $valorSeleccionado = $request->input('pdfreclamacion');
        if($valorSeleccionado=='todos'){
            $registros = SegReclamaciones::with(['asegurado.terceroAF', 'asegurado.tercero', 'cobertura'])->get();
        }
        elseif($valorSeleccionado=='af'){
            $registros = SegReclamaciones::whereHas('asegurado', function ($query) {
                $query->where('parentesco', 'AF');
            })->with(['asegurado.terceroAF', 'asegurado.tercero', 'cobertura'])->get();
        }
        elseif($valorSeleccionado=='co'){
            $registros = SegReclamaciones::whereHas('asegurado.tercero', function ($query) {
                $query->where('genero', 'H');  // Condición para la relación 'tercero'
            })->with(['asegurado.terceroAF', 'asegurado.tercero', 'cobertura'])->get();
        }       
        $pdf = Pdf::loadView('seguros.reclamaciones.pdf', 
            ['registros' => $registros,'image_path' => public_path('assets/images/CORPENTUNIDA_LOGO PRINCIPAL  (2).png'),])
                ->setPaper('letter', 'landscape');
        return $pdf->download(date('Y-m-d') . " Reporte.pdf");
        //return view('seguros.reclamaciones.pdf', ['registros' => $registros, 'image_path' => public_path('assets/images/CORPENTUNIDA_LOGO PRINCIPAL  (2).png')]);
    }
}
