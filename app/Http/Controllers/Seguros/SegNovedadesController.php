<?php

namespace App\Http\Controllers\Seguros;

use App\Http\Controllers\Controller;
use App\Models\Seguros\SegBeneficios;
use App\Models\Seguros\SegCondicion;
use App\Models\Seguros\SegNovedades;
use App\Models\Seguros\SegAsegurado;
use App\Models\Seguros\SegPlan;
use App\Models\Seguros\SegPoliza;
use App\Models\Seguros\SegCambioEstadoNovedad;
use App\Http\Controllers\Exequial\ComaeTerController;
use App\Models\Maestras\maeTerceros;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\AuditoriaController;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\ExcelExport;

class SegNovedadesController extends Controller
{
    private function auditoria($accion)
    {
        $auditoriaController = app(AuditoriaController::class);
        $auditoriaController->create($accion, 'SEGUROS');
    }
    public function index(Request $request)
    {
        $estado = $request->query('estado', 'nuevas');
        $colecciones = [
            'nuevas' => SegNovedades::where('estado', 1)
                ->with(['tercero', 'cambiosEstado', 'beneficiario'])
                ->get(),
            'radicado' => SegNovedades::where('estado', 2)
                ->with(['tercero', 'cambiosEstado'])
                ->get(),
            'complementos' => SegNovedades::where('estado', 5)
                ->with(['tercero', 'cambiosEstado'])
                ->get(),
            'aprobado' => SegNovedades::where('estado', 3)
                ->with(['tercero', 'cambiosEstado'])
                ->get(),
            'rechazado' => SegNovedades::where('estado', 4)
                ->with(['tercero', 'cambiosEstado'])
                ->get(),
        ];

        $data = $colecciones[$estado] ?? $colecciones['nuevas'];
        return view('seguros.novedades.index', compact('data', 'estado'));
    }

    public function create()
    {
        return view('seguros.novedades.create');
    }

    public function show(Request $request)
    {
        $id = $request->query('a');
        $isactive = SegPoliza::where('seg_asegurado_id', $id)->where('seg_convenio_id', '23055455')->first();
        if (!$isactive || !$isactive->active) {
            return back()->with('error', 'La póliza no existe o está inactiva.');
        }
        $asegurado = SegAsegurado::where('cedula', $id)
            ->with(['tercero', 'terceroAF', 'polizas.plan'])
            ->first();
        $edad = date_diff(date_create($asegurado->tercero_preferido->fec_nac), date_create('today'))->y;
        $idcondicion = app(SegPlanController::class)->getCondicion($edad);

        $planes = SegPlan::where('condicion_corpen', $idcondicion)
            ->where('vigente', true)
            ->with(['convenio'])
            ->get();

        $condicion = SegCondicion::where('id', $idcondicion)->first(['descripcion']);
        $tab = $request->activeTab ?? 'generalTab';
        $grupoFamiliar = SegAsegurado::where('Titular', $asegurado->titular)->with('tercero', 'polizas.plan.coberturas')->get();
        $totalPrima = DB::table('SEG_polizas')->whereIn('seg_asegurado_id', $grupoFamiliar->pluck('cedula'))->sum('valor_prima');
        $totalPrimaCorpen = DB::table('SEG_polizas')->whereIn('seg_asegurado_id', $grupoFamiliar->pluck('cedula'))->sum('primapagar');
        //$reclamaciones = SegReclamaciones::where('cedulaAsegurado', $asegurado->cedula)->with(['cobertura', 'diagnostico'])->get();
        return view('seguros.novedades.create', compact('asegurado', 'planes', 'condicion', 'grupoFamiliar', 'totalPrima', 'totalPrimaCorpen'))->with('activeTab', $tab);
    }

    public function store(Request $request)
    {
        /*$request->validate([
            'formulario_nov' => 'required|mimes:pdf|max:2048',
        ]);*/
        $formulario = null;
        if ($request->hasFile('formulario_nov')) {
            $formulario = $request->file('formulario_nov');
        }
        $plan = SegPlan::findOrFail($request->planid);
        $novedad = SegNovedades::create([
            'id_poliza' => $request->id_poliza ?? null,
            'id_asegurado' => $request->asegurado,
            'tipo' => $request->tipoNovedad,
            'estado' => $request->estado ?? 1,
            'id_plan' => $plan->id,
            'valorAsegurado' => $plan->valor,
            'primaAseguradora' => $plan->prima_aseguradora,
            'primaCorpen' => $request->primacorpen,
            'extraprima' => $request->extra_prima,
        ]);
        if ($formulario) {
            $ruta = Storage::disk('s3')->put('corpentunida/seguros_vida/' . $novedad->id, $formulario);
            $novedad->update(['formulario' => $ruta]);
        }
        SegCambioEstadoNovedad::create([
            'novedad' => $novedad->id,
            'estado' => $request->estado,
            'observaciones' => strtoupper($request->observaciones),
            'fechaInicio' => Carbon::now()->toDateString(),
        ]);
        if ($request->tipoNovedad === '1') {
            $accion = 'modificacion en poliza  ' . $request->id_poliza . ' Asegurado ' . $request->asegurado;
        } elseif ($request->tipoNovedad === '2') {
            /*$controllerapi = new ComaeTerController();
            $terapi = $controllerapi->show($request->asegurado);
            if ($terapi->getStatusCode() === 404) {
                return redirect()->back()->with('error', 'La cédula ingresada no coincide con ningún documento en base de datos de SiaSoft.');
            }*/
            $terceroontable = maeTerceros::where('cod_ter', $request->asegurado)->exists();
            if (!$terceroontable) {
                $terceroontable = maeTerceros::create([
                    'cod_ter' => $request->asegurado,
                    'nom_ter' => strtoupper($request->ternombre),
                    'fec_nac' => $request->fechaNacimiento,
                    'tel1' => $request->tertelefono,
                    'sexo' => $request->tergenero,
                    'cod_dist' => $request->terdistrito,
                ]);
            } else {
                $terceroontable = maeTerceros::where('cod_ter', $request->asegurado)->first();
            }
            $aseguradontable = SegAsegurado::where('cedula', $terceroontable->cod_ter)->first();
            if (!$aseguradontable) {
                if ($request->estitular === '1') {
                    $asegurado = SegAsegurado::create([
                        'cedula' => $terceroontable->cod_ter,
                        'parentesco' => 'AF',
                        'titular' => $terceroontable->cod_ter,
                        'valorpAseguradora' => $request->valorpagaraseguradora,
                    ]);
                } else {
                    $asegurado = SegAsegurado::create([
                        'cedula' => $terceroontable->cod_ter,
                        'parentesco' => $request->parentesco,
                        'titular' => $request->titularasegurado,
                    ]);
                }
            }
            $this->auditoria('TERCERO CREAD0 ID ' . $terceroontable->cod_ter);
            $this->auditoria('ASEGURADO CREADO ID ' . $asegurado->cedula);
        }
        return redirect()->route('seguros.novedades.index')->with('success', 'Novedad registrada correctamente');
    }

    public function verArchivo($id)
    {
        $novedad = SegNovedades::findOrFail($id);
        if (!$novedad->formulario || !Storage::exists($novedad->formulario)) {
            abort(404);
        }
        return response()->file(storage_path('app/' . $novedad->formulario));
    }

    public function edit($id)
    {
        $novedad = SegNovedades::with(['poliza', 'estadoNovedad', 'cambiosEstado'])->findOrFail($id);
        $actividadnov = SegCambioEstadoNovedad::where('novedad', $id)->with('estadosname')->get();
        if ($novedad->estado != 1) {
            $editar = false;
            return view('seguros.novedades.edit', compact('novedad', 'editar', 'actividadnov'))->with('error', 'La novedad no esta en estado nueva solicitud. No se puede editar.');
            //return back()->with('error', 'La novedad no esta en estado nueva solicitud. No se puede editar.');
        }
        if ($novedad->tipo == 4) {
            return redirect()->route('seguros.beneficiario.edit', $novedad->beneficiario_id);
            //return redirect()->route('seguros.novedades.index')->with('error', 'La novedad de ingresar beneficiario no se puede editar.');
        }
        $editar = true;
        return view('seguros.novedades.edit', compact('novedad', 'editar', 'actividadnov'));
    }

    public function update(Request $request, $id)
    {
        if ($request->has('individual') || $request->individual) {
            $novedad = SegNovedades::findOrFail($id);
            $novedad->update([
                'primaCorpen' => $request->primaPagar,
                'extraprima' => $request->extraprima,
            ]);
            SegCambioEstadoNovedad::create([
                'novedad' => $id,
                'estado' => $request->estado,
                'observaciones' => $request->observaciones,
                'fechaInicio' => Carbon::now()->toDateTimeString(),
            ]);
            $this->auditoria('SE EDITO UNA NOVEDAD, ID: ' . $id);
            return redirect()->route('seguros.novedades.index')->with('success', 'Se actualizó correctamente la novedad.');
        } else {
            foreach ($request->ids as $id) {
                $novedad = SegNovedades::findOrFail($id);
                if ($request->estado == 3) {
                    if ($novedad->tipo == 1) {
                        $poliza = SegPoliza::findOrFail($novedad->id_poliza);
                        $poliza->update([
                            'valor_prima' => $novedad->primaAseguradora,
                            'seg_plan_id' => $novedad->id_plan,
                            'extra_prima' => $novedad->extraprima,
                            'primapagar' => $novedad->primaCorpen,
                            'valor_asegurado' => $novedad->valorAsegurado,
                        ]);
                    } elseif ($novedad->tipo == 2) {
                        SegPoliza::create([
                            'seg_convenio_id' => '23055455',
                            'seg_asegurado_id' => $novedad->id_asegurado,
                            'seg_plan_id' => $novedad->id_plan,
                            'valor_prima' => $novedad->primaAseguradora,
                            'extra_prima' => $novedad->extraprima,
                            'primapagar' => $novedad->primaCorpen,
                            'valor_asegurado' => $novedad->valorAsegurado,
                            'active' => true,
                            'fecha_inicio' => Carbon::now()->toDateTimeString(),
                        ]);
                    } elseif ($novedad->tipo == 3) {
                        $poliza = SegPoliza::findOrFail($novedad->id_poliza);
                        $poliza->update([
                            'active' => false,
                            'fecha_fin' => Carbon::now()->toDateTimeString(),
                        ]);
                    } elseif ($novedad->tipo == 4) {
                        $beneficiario = SegBeneficiario::findOrFail($novedad->beneficiario_id);
                        $beneficiario->update(['activo' => 1]);
                    }
                }
                $novedad->update([
                    'estado' => $request->estado,
                ]);
                SegCambioEstadoNovedad::create([
                    'novedad' => $id,
                    'estado' => $request->estado,
                    'observaciones' => strtoupper($request->observaciones),
                    'fechaInicio' => Carbon::now()->toDateTimeString(),
                    'fechaCierre' => Carbon::now()->toDateTimeString(),
                ]);
                $this->auditoria('SE ACTUALIZO EL ESTADO NOVEDAD, ID: ' . $novedad->id);
            }
            return redirect()->route('seguros.novedades.index')->with('success', 'Se actualizó correctamente el cambio de estado.');
        }
    }
    public function destroy(Request $request, $id)
    {
        //registrar una novedad de retiro
        /*$request->validate([
            'formulario_nov' => 'required|mimes:pdf|max:2048',
        ]);*/
        $formulario = null;
        if ($request->hasFile('formulario_nov')) {
            $formulario = Storage::disk('s3')->put('corpentunida/seguros_vida/novedades/' . $id, $request->file('formulario_nov'));
        }
        $novedad = SegNovedades::create([
            'id_poliza' => $request->id_poliza ?? null,
            'id_asegurado' => $id,
            'tipo' => $request->tipoNovedad,
            'estado' => 1,
            'id_plan' => $request->planid,
            'formulario' => $formulario,
        ]);
        SegCambioEstadoNovedad::create([
            'novedad' => $novedad->id,
            'estado' => $request->estado,
            'observaciones' => strtoupper($request->observacionretiro),
            'fechaIncio' => Carbon::now()->toDateTimeString(),
        ]);
        return redirect()->route('seguros.novedades.index')->with('success', 'Novedad registrada correctamente');
    }

    public function descargarexcel()
    {
        $datos = SegNovedades::where('estado', '!=', 3)
            ->with(['tercero', 'cambiosEstado', 'asegurado'])
            ->get();
        //dd($datos);
        $headings = ['CEDULA', 'NOMBRE', 'FECHA NACIMIENTO', 'EDAD', 'FECHA SOLICITUD', 'TIPO NOVEDAD', 'VALOR ASEGURADO SOLICITADO', 'GENERO', 'PARENTESCO', 'OBSERVACIONES'];
        $datosFormateados = $datos->map(function ($item) {
            $fechaNacimiento = Carbon::parse($item->fecha_nacimiento);
            return [
                'cedula' => $item->id_asegurado,
                'nombre' => $item->nombre_tercero ?? '',
                'fecha_nacimiento' => $fechaNacimiento->format('d/m/Y'),
                'edad' => $item->edad,
                'fecha_solicitud' => $item->created_at ? $item->created_at->format('d/m/Y') : '',
                'tipo_novedad' => $item->tipoNovedad->nombre ?? '',
                'valor_asegurado' => $item->valorAsegurado ?? 0,
                'genero' => $item->tercero->sexo ?? '',
                'parentesco' => $item->asegurado->parentesco ?? '',
                'observaciones' => $item->cambiosEstado->last()->observaciones ?? '',
            ];
        });
        return Excel::download(new ExcelExport($datosFormateados, $headings), date('Y-m-d') . ' NOVEDADES.xlsx');
    }
}
