<?php

namespace App\Http\Controllers\Seguros;

use App\Http\Controllers\Controller;
use App\Models\Seguros\SegPoliza;
use App\Models\Seguros\SegReclamaciones;
use App\Models\Seguros\SegAsegurado;
use App\Models\Seguros\SegCobertura;
use App\Models\Seguros\SegEstadoReclamacion;
use App\Models\Seguros\SegCambioEstadoReclamacion;
use App\Models\Seguros\SegDiagnosticos;
use App\Http\Controllers\AuditoriaController;
use App\Http\Controllers\Exequial\ParentescosController;
use App\Exports\ExportSegInformeReclamacion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\ExcelExport;
use Carbon\Carbon;

class SegReclamacionesController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    private function auditoria($accion)
    {
        $auditoriaController = app(AuditoriaController::class);
        $auditoriaController->create($accion, 'SEGUROS');
    }
    public function index()
    {
        Carbon::setLocale('es');
        $rec = SegReclamaciones::with(['asegurado.terceroAF', 'asegurado.tercero', 'cobertura', 'tercero'])->get();
        $reclamaciones = $rec->map(function ($reclamacion) {
            $tiempoTranscurrido = Carbon::parse($reclamacion->updated_at)->diffForHumans();
            $reclamacion->tiempo_transcurrido = $tiempoTranscurrido;
            return $reclamacion;
        });
        $coberturas = SegCobertura::select('seg_coberturas.*', DB::raw('COUNT(SEG_reclamaciones.idCobertura) as reclamaciones_count'))->leftJoin('SEG_reclamaciones', 'seg_coberturas.id', '=', 'SEG_reclamaciones.idCobertura')->groupBy('seg_coberturas.id')->orderByDesc('reclamaciones_count')->first();
        $counts = SegReclamaciones::with('tercero')
            ->get()
            ->filter(function ($reclamacion) {
                return $reclamacion->tercero !== null;
            })
            ->groupBy(function ($reclamacion) {
                return $reclamacion->tercero->sexo;
            });
        $hombres = $counts->get('V', collect())->count();
        $mujeres = $counts->get('H', collect())->count();

        $vencidas = $this->listaReclamacionesVencidas();
        return view('seguros.reclamaciones.index', compact('reclamaciones', 'coberturas', 'mujeres', 'hombres', 'vencidas'));
    }

    private function listaReclamacionesVencidas()
    {
        Carbon::setLocale('es');
        $reclamaciones = SegReclamaciones::where('updated_at', '<', Carbon::now()->subMonths(4))
            ->where('estado', '!=', 4)
            ->get();

        $reclamacionesFormateadas = $reclamaciones->map(function ($reclamacion) {
            $tiempoTranscurrido = Carbon::parse($reclamacion->updated_at)->diffForHumans();
            $reclamacion->tiempo_transcurrido = $tiempoTranscurrido;
            return $reclamacion;
        });
        return $reclamacionesFormateadas;
    }

    public function create(Request $request)
    {
        $id = $request->query('a');
        $poliza = SegPoliza::where('seg_asegurado_id', $id)->where('seg_convenio_id', '23055455')->first();
        if (!$poliza->active) {
            abort(403, 'La póliza está inactiva.');
        }
        $asegurado = SegAsegurado::where('cedula', $id)
            ->with(['tercero', 'terceroAF'])
            ->first();
        $poliza->load(['plan.coberturas']);
        $estados = SegEstadoReclamacion::all();
        $parentescos = $controllerparen = app()->make(ParentescosController::class);
        $parentescos = $parentescos->index();
        $diagnosticos = SegDiagnosticos::orderBy('diagnostico', 'asc')->get();
        $reclamaciones = SegReclamaciones::where('cedulaAsegurado', $asegurado->cedula)
            ->with(['cobertura', 'diagnostico'])
            ->get();
        return view('seguros.reclamaciones.create', compact('asegurado', 'poliza', 'estados', 'parentescos', 'diagnosticos', 'reclamaciones'));
    }

    /**
     * Store a newly created resource in storage.
     */

    public function store(Request $request)
    {        
        $data = [
            'cedulaAsegurado' => $request->asegurado,
            'idCobertura' => $request->cobertura_id,
            'fechaSiniestro' => $request->fechasiniestro,
            'fechaContacto' => $request->fechacontacto,
            'horaContacto' => $request->horacontacto,
            'cedulaContacto' => $request->cedulacontacto,
            'nombreContacto' => strtoupper($request->nombrecontacto),
            'telcontacto' => $request->telcontacto,
            'parentescoContacto' => $request->parentesco_id,
            'estado' => $request->estado_id,
            'poliza_id' => $request->poliza_id,
            'valor_asegurado' => $request->valorAsegurado,
            'porreclamar' => $request->porValorAsegurado,
            'fecha_desembolso' => $request->fechadesembolso,
        ];
        if (is_null($request->diagnostico_id)) {
            if ($request->boolean('adddiagnostico')) {
                $diag = SegDiagnosticos::create([
                    'diagnostico' => strtoupper($request->otrodiagnostico),
                ]);
                $data['idDiagnostico'] = $diag->id;
            } else {
                $data['otro'] = $request->otrodiagnostico;
            }
        } else {
            $data['idDiagnostico'] = $request->diagnostico_id;
        }
        $polizares = SegPoliza::where('id', $request->poliza_id);
        if ($request->boolean('finreclamacion')) {
            $data['finReclamacion'] = true;
            if ($request->cobertura_id == '1') {
                $polizares->update(['active' => false]);
            }
        }
        $reclamacion = SegReclamaciones::create($data);
        if ($request->boolean('cambiovalasegurado') && $request->estado_id == '4') {
            $valor = $request->valorAsegurado - ($request->valorAsegurado * $request->porValorAsegurado) / 100;
            $polizares->update(['valor_asegurado' => $valor]);
        }
        if($request->boolean('confirmviuda')){
            $asegurado = SegAsegurado::where('titular', $request->asegurado)->where('parentesco', 'CO')->first();
            if ($asegurado) {$asegurado->update(['viuda' => true]);}
        }
        $cambioEstado = SegCambioEstadoReclamacion::create([
            'reclamacion_id' => $reclamacion->id,
            'estado_id' => $request->estado_id,
            'observacion' => strtoupper($request->observacion),
            'fecha_actualizacion' => now()->toDateString(),
            'hora_actualizacion' => now()->toTimeString(),
        ]);

        $url = route('seguros.poliza.show', ['poliza' => 'ID']) . '?id=' . $request->asegurado;
        if ($reclamacion && $cambioEstado) {
            $accion = 'add nueva reclamacion  ' . $request->asegurado;
            $this->auditoria($accion);
            return redirect()->to($url)->with('success', 'Proceso de reclamación añadido exitosamente.');
        } else {
            return redirect()->to($url)->with('error', 'No se pudo agregar la reclamación, intente mas tarde.');
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id) {}

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(SegReclamaciones $reclamacion)
    {
        $asegurado = SegAsegurado::where('cedula', $reclamacion->cedulaAsegurado)
            ->with(['tercero', 'terceroAF'])
            ->first();
        $poliza = SegPoliza::where('seg_asegurado_id', $reclamacion->cedulaAsegurado)
            ->with(['plan.coberturas'])
            ->first();
        $estados = SegEstadoReclamacion::all();
        $hisreclamacion = SegCambioEstadoReclamacion::where('reclamacion_id', $reclamacion->id)
            ->with(['estado'])
            ->get();
        $diagnosticos = SegDiagnosticos::orderBy('diagnostico', 'asc')->get();

        return view('seguros.reclamaciones.edit', compact('reclamacion', 'asegurado', 'poliza', 'estados', 'hisreclamacion', 'diagnosticos'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $dataupdate = [
            'estado' => $request->estado_id,
        ];
        if (!empty($request->fechadesembolso) && $request->estado_id == 4) {
            $dataupdate['finReclamacion'] = true;
            $dataupdate['fecha_desembolso'] = $request->fechadesembolso;
        }
        $update = SegReclamaciones::where('id', $id)->update($dataupdate);

        //crear registro cambio de estado de la reclamacion
        $crear = SegCambioEstadoReclamacion::create([
            'estado_id' => $request->estado_id,
            'reclamacion_id' => $id,
            'observacion' => strtoupper($request->observacion),
            'fecha_actualizacion' => now()->toDateString(),
            'hora_actualizacion' => now()->toTimeString(),
        ]);

        if ($request->checkchangevalaseg == '1') {
            $poliza = SegPoliza::find($request->poliza_id);
            $nuevoMonto = $poliza->valor_asegurado * 0.5;
            $poliza->valor_asegurado = $nuevoMonto;
            $poliza->save();
            SegReclamaciones::where('id', $id)->update([
                'valor_asegurado' => $poliza->valor_asegurado,
            ]);
        }

        if ($update && $crear) {
            $accion = 'update reclamacion id ' . $id;
            $this->auditoria($accion);
            return redirect()->route('seguros.reclamacion.index')->with('success', 'Reclamación actualizada exitosamente.');
        } else {
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

    public function generarpdf(Request $request)
    {
        $valorSeleccionado = $request->input('opcion');
        if ($valorSeleccionado == 'todos') {
            $registros = SegReclamaciones::with(['asegurado.terceroAF', 'asegurado.tercero', 'cobertura'])
                ->whereYear('fechaSiniestro', $request->anio)
                ->get();
        } elseif ($valorSeleccionado == 'af') {
            $registros = SegReclamaciones::whereHas('asegurado', function ($query) {
                $query->where('parentesco', 'AF');
            })
                ->with(['asegurado.terceroAF', 'asegurado.tercero', 'cobertura'])
                ->whereYear('fechaSiniestro', $request->anio)
                ->get();
        } elseif ($valorSeleccionado == 'co') {
            $registros = SegReclamaciones::whereHas('asegurado.tercero', function ($query) {
                $query->where('sexo', 'H');
            })
                ->with(['asegurado.terceroAF', 'asegurado.tercero', 'cobertura'])
                ->whereYear('fechaSiniestro', $request->anio)
                ->get();
        }
        $pdf = Pdf::loadView('seguros.reclamaciones.pdf', ['registros' => $registros, 'image_path' => public_path('assets/images/CORPENTUNIDA_LOGO PRINCIPAL  (2).png')])->setPaper('letter', 'landscape');
        return $pdf->download(date('Y-m-d') . ' Reporte.pdf');
        //return view('seguros.reclamaciones.pdf', ['registros' => $registros, 'image_path' => public_path('assets/images/CORPENTUNIDA_LOGO PRINCIPAL  (2).png')]);
    }

    public function exportexcel()
    {
        $datos = SegReclamaciones::with(['asegurado.terceroAF', 'asegurado.tercero', 'cobertura', 'diagnostico'])->get();
        $headings = ['N°', 'ASEGURADO CÉDULA', 'ASEGURADO', 'EDAD', 'TITULAR CEDULA', 'TITULAR', 'PARENTESCO', 'COBERTURA', 'VALOR ASEGURADO', 'DIAGNOSTICO', 'FECHA ACTUALIZACIÓN', 'ESTADO'];
        $datosFormateados = $datos->map(function ($item, $index) {
            $fechaNacimiento = Carbon::parse($item->fecha_nacimiento);            
            return [
                'N°' => $index + 1,
                'ASEGURADO CÉDULA' => $item->cedulaAsegurado ?? '',
                'ASEGURADO' => $item->nombre_tercero ?? '',
                'EDAD' => $fechaNacimiento ?? ' ',
                'TITULAR CEDULA' => $item->asegurado->terceroAF->cod_ter ?? '',
                'TITULAR' => $item->asegurado->nombre_titular ?? '',
                'PARENTESCO' => $item->asegurado->parentesco ?? '',
                'COBERTURA' => $item->cobertura->nombre,
                'VALOR ASEGURADO' => $item->valor_asegurado ?? '',
                'DIAGNOSTICO' => $item->diagnostico->diagnostico ?? '',
                'FECHA ACTUALIZACIÓN' => $item->updated_at,
                'ESTADO' => $item->estadoReclamacion->nombre,
            ];
        });
        return Excel::download(new ExcelExport($datosFormateados, $headings), date('Y-m-d') . ' Reporte.xlsx');
    }

    public function dashboard()
    {
        $data = SegReclamaciones::select('er.nombre as estado_nombre', DB::raw('COUNT(*) as total'))->join('SEG_estadoReclamacion as er', 'er.id', '=', 'SEG_reclamaciones.estado')->groupBy('er.nombre')->get();
        $arraydata = [
            'labels' => $data->pluck('estado_nombre')->toArray(),
            'valores' => $data->pluck('total')->toArray(),
        ];
        $totalreclamaciones = SegReclamaciones::count();
        $registrosPorEstado = SegReclamaciones::with('cobertura', 'tercero')->select('SEG_reclamaciones.*', 'er.nombre as estado_nombre')->join('SEG_estadoReclamacion as er', 'er.id', '=', 'SEG_reclamaciones.estado')->orderBy('er.nombre')->get()->groupBy('estado_nombre');

        $registrosporcobertura = SegReclamaciones::select('c.nombre as cobertura_nombre', DB::raw("SUM(CASE WHEN t.sexo = 'V' THEN 1 ELSE 0 END) as hombres"), DB::raw("SUM(CASE WHEN t.sexo = 'H' THEN 1 ELSE 0 END) as mujeres"), DB::raw('SUM(CASE WHEN t.sexo IS NULL THEN 1 ELSE 0 END) as sin_genero'))->join('seg_coberturas as c', 'c.id', '=', 'SEG_reclamaciones.idCobertura')->leftJoin('MaeTerceros as t', 't.cod_ter', '=', 'SEG_reclamaciones.cedulaAsegurado')->groupBy('c.nombre')->get();
        return view('seguros.reclamaciones.dashboard', compact('arraydata', 'totalreclamaciones', 'registrosPorEstado', 'registrosporcobertura'));
    }

    public function exportarInformeCompleto()
    {
        $registrosPorEstado = SegReclamaciones::with('cobertura', 'tercero', 'cambiosEstado')->select('SEG_reclamaciones.*', 'er.nombre as estado_nombre')->join('SEG_estadoReclamacion as er', 'er.id', '=', 'SEG_reclamaciones.estado')->orderBy('er.nombre')->get()->groupBy('estado_nombre');
        return Excel::download(new ExportSegInformeReclamacion($registrosPorEstado), 'informe_siniestros.xlsx');
    }
}
