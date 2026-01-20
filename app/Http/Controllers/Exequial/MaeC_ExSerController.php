<?php

namespace App\Http\Controllers\Exequial;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Exequiales\ExMonitoria;
use App\Models\Exequiales\Parentescos;
use App\Models\Exequiales\ExServicioComentarios;
use Illuminate\Support\Facades\Http;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use App\Http\Controllers\AuditoriaController;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\ExcelExport;
use App\Models\Exequiales\ComaeExRelPar;
use Illuminate\Support\Facades\DB;

class MaeC_ExSerController extends Controller
{
    private function auditoria($accion, $area)
    {
        $auditoriaController = app(AuditoriaController::class);
        $auditoriaController->create($accion, $area);
    }
    public function index()
    {
        $registros = ExMonitoria::orderBy('id', 'desc')->get();
        $controllerparentesco = app()->make(ParentescosController::class);
        foreach ($registros as $registro) {
            $nomPar = $controllerparentesco->showName($registro->parentesco);
            $registro->parentesco = $nomPar;
        }
        $mReg = ExMonitoria::whereMonth('fechaFallecimiento', Carbon::now()->month)->count();
        $nmen = ExMonitoria::where('genero', 'M')->count();
        $nwomen = ExMonitoria::where('genero', 'F')->count();

        return view('exequial.prestarServicio.index', compact('registros', 'mReg', 'nmen', 'nwomen'));
    }

    public function edit($id)
    {
        $registro = ExMonitoria::with('comments')->findOrFail($id);

        $controllerparentesco = app()->make(ParentescosController::class);
        $nomPar = $controllerparentesco->showName($registro->parentesco);
        $registro->parentesco = $nomPar;

        $regiones = DB::table('MaeRegiones')->orderBy('nombre')->get();
        $municipio = DB::table('MaeMunicipios')->where('id', $registro->municipio)->first();
        $departamento = $municipio ? DB::table('MaeDepartamentos')->where('codigo_Dane', $municipio->id_departamento)->first() : null;
        $regionsel = $departamento ? DB::table('MaeRegiones')->where('id', $departamento->id_region)->first() : null;
        return view('exequial.prestarServicio.edit', compact('registro', 'regiones', 'municipio', 'departamento', 'regionsel'));
    }

    public function store(Request $request)
    {
        //$this->authorize('create', auth()->user());
        $token = env('TOKEN_ADMIN');
        $fechaActual = Carbon::now();
        if ($request->pastor === 'true') {
            $codPar = null;
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $token,
                'Accept' => '*/*',
            ])->patch(env('API_PRODUCCION') . '/api/Exequiales/Tercero', [
                'documentId' => $request->cedulaTitular,
                'dateInit' => $request->dateInit ? $request->dateInit : ' ',
                'codePlan' => $request->codePlan ? $request->codePlan : '01',
                'discount' => $request->discount,
                'observation' => 'PASTOR FALLECIDO' . $request->observation,
                'stade' => false,
            ]);
            ComaeExCli::where('cod_cli', $request->cedulaTitular)->update(['estado' => false]);
            $request->parentesco = 'TITULAR';
        } else {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $token,
                'Accept' => '*/*',
            ])->put(env('API_PRODUCCION') . '/api/Exequiales/Beneficiary', [
                'name' => $request->nameBeneficiary,
                'codeParentesco' => $request->parentesco,
                'type' => 'I',
                'dateEntry' => $request->dateInit ? $request->dateInit : ' ',
                'documentBeneficiaryId' => $request->cedulaFallecido,
                'dateBirthDate' => $request->fecNacFallecido,
            ]);
            ComaeExRelPar::where('cod_cli', $request->cedula)->update([
                'estado' => false,
                'tipo' => 'I',
            ]);
        }

        if ($response->successful()) {
            ExMonitoria::create([
                'fechaRegistro' => $fechaActual,
                'horaFallecimiento' => $request->horaFallecimiento,
                'cedulaTitular' => $request->cedulaTitular,
                'nombreTitular' => $request->nameTitular,
                'nombreFallecido' => strtoupper($request->nameBeneficiary),
                'cedulaFallecido' => $request->cedulaFallecido,
                'fechaFallecimiento' => $request->fechaFallecimiento,
                'lugarFallecimiento' => strtoupper($request->lugarFallecimiento),
                'parentesco' => $request->parentesco,
                'estado' => $request->estadonuevo,
                'contacto' => strtoupper($request->contacto),
                'telefonoContacto' => $request->telefonoContacto,
                'Contacto2' => strtoupper($request->contacto2),
                'telefonoContacto2' => $request->telefonoContacto2,
                'genero' => $request->genero,
            ]);
            $accion = 'prestar servicio a ' . $request->cedulaFallecido;
            $this->auditoria($accion, 'EXEQUIALES');

            $url = route('exequial.asociados.show', ['asociado' => 'ID']) . '?id=' . $request->cedulaTitular;
            return redirect()->to($url)->with('success', 'Se registro existosamente el servicio.');
        } else {
            return 'error ' . response()->json(['error' => $response->json()], $response->status());
        }
    }

    public function consultaMes($mes)
    {
        $filtromes = ExMonitoria::whereMonth('fechaFallecimiento', $mes)->get();
        return view('exequial.prestarServicio.index', ['registros' => $filtromes]);
    }

    /* public function exportData()
    {
        return Excel::download(new DataExport, 'data.xlsx');
    } */

    public function generarpdf()
    {
        $registros = ExMonitoria::orderBy('id', 'desc')->get();
        $controllerparentesco = app()->make(ParentescosController::class);
        foreach ($registros as $registro) {
            $nomPar = $controllerparentesco->showName($registro->parentesco);
            $registro->parentesco = $nomPar;
        }
        $pdf = Pdf::loadView('exequial.prestarServicio.indexpdf', ['registros' => $registros, 'image_path' => public_path('assets/images/CORPENTUNIDA_LOGO PRINCIPAL  (2).png')])->setPaper('letter', 'landscape');
        return $pdf->download(date('Y-m-d') . ' Reporte.pdf');
        //return view('exequial.prestarServicio.indexpdf', ['registros' => $registros, 'image_path' => public_path('assets/images/CORPENTUNIDA_LOGO PRINCIPAL  (2).png')]);
    }

    public function generarExcelPrestarServicio()
    {
        Carbon::setLocale('es');
        $registros = ExMonitoria::orderBy('id', 'desc')->get();
        $data = [];
        $i = 1;
        $controllerparentesco = app()->make(ParentescosController::class);
        foreach ($registros as $reg) {
            $reg->parentesco = $controllerparentesco->showName($reg->parentesco);
            $data[] = [$i++, $reg->fechaFallecimiento, Carbon::parse($reg->fechaFallecimiento)->translatedFormat('l'), $reg->horaFallecimiento, $reg->cedulaTitular, $reg->nombreTitular, $reg->cedulaFallecido, $reg->nombreFallecido, $reg->lugarFallecimiento, $reg->parentesco, $reg->contacto, $reg->telefonoContacto, $reg->estado == 1 ? 'PENDIENTE' : ($reg->estado == 2 ? 'EN PROCESO' : 'CERRADO')];
        }

        $headings = ['Nº', 'Fecha Fallecimiento', 'Día', 'Hora', 'Cédula Titular', 'Nombre Titular', 'Cédula Fallecido', 'Nombre Fallecido', 'Lugar Fallecimiento', 'Parentesco', 'Contacto', 'Teléfono', 'Estado'];
        $name = Carbon::now()->format('Y-m-d_H-i-s') . 'Prestar_Servicio.xlsx';
        return Excel::download(new ExcelExport($data, $headings), $name);
    }

    public function reporteIndividual($id)
    {
        $registro = ExMonitoria::find($id);
        $controllerparentesco = app()->make(ParentescosController::class);
        $nomPar = $controllerparentesco->showName($registro->parentesco);
        $registro->parentesco = $nomPar;

        $pdf = Pdf::loadView('exequial.prestarServicio.indexpdf', ['registros' => collect([$registro]), 'image_path' => public_path('assets/img/corpentunida-logo-azul-oscuro-2021x300.png')])->setPaper('letter', 'landscape');
        return $pdf->download(date('Y-m-d') . ' ' . $registro->cedulaFallecido . ' Reporte.pdf');

        // return view('exequial.prestarServicio.indexpdf', [
        //     'registros' => collect([$registro]),
        //     'image_path' => public_path('assets/img/corpentunida-logo-azul-oscuro-2021x300.png'),]);
    }

    public function update(Request $request, $id)
    {
        $updated = ExMonitoria::where('id', $id)->update([
            'horaFallecimiento' => $request->horaFallecimiento,
            'fechaFallecimiento' => $request->fechaFallecimiento,
            'lugarFallecimiento' => $request->lugarFallecimiento,
            'contacto' => $request->contacto,
            'telefonoContacto' => $request->telefonoContacto,
            'Contacto2' => $request->contacto2,
            'telefonoContacto2' => $request->telefonoContacto2,
            'municipio' => $request->municipioid,
        ]);
        if ($updated > 0) {
            $accion = 'actualizar prestar servicio a ' . $request->cedulaFallecido;
            $this->auditoria($accion, 'EXEQUIALES');
            return redirect()->route('exequial.prestarServicio.index')->with('success', 'Registro actualizado exitosamente');
        } else {
            return redirect()->route('exequial.prestarServicio.index')->with('error', 'Error al actualizar el registro');
            //return response()->json(['error' => 'Error al actualizar el registro'], 500);
        }
    }

    public function destroy($id)
    {
        $token = env('TOKEN_ADMIN');
        $fechaActual = now()->toDateString();
        $nombreFallecido = ExMonitoria::find($id)->value('nombreFallecido');
        $parentesco = ExMonitoria::find($id)->value('parentesco');
        $cedulaFallecido = ExMonitoria::find($id)->value('cedulaFallecido');
        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $token,
            'Accept' => 'application/json',
        ])->put(env('API_PRODUCCION') . '/api/Exequiales/Beneficiary', [
            'name' => $nombreFallecido,
            'codeParentesco' => $parentesco,
            'type' => 'A',
            'dateEntry' => $fechaActual,
            'documentBeneficiaryId' => $cedulaFallecido,
            'dateBirthDate' => $fechaActual,
        ]);
        if ($response->successful()) {
            return response()->json([
                'status' => $response->status(),
                'headers' => $response->headers(),
                'body' => $response->body(),
                'json' => $response->json(),
                'successful' => $response->successful(),
                'serverError' => $response->serverError(),
                'clientError' => $response->clientError(),
            ]);
        } else {
            return response()->json(['error' => 'error al cambiar estado'], 500);
        }
    }

    public function addComment(Request $request, $id)
    {
        $comment = ExServicioComentarios::create([
            'id_exser' => $id,
            'tipo' => $request->tipocomment,
            'fecha' => Carbon::now(),
            'observacion' => $request->observacioncomment,
        ]);
        if ($comment) {
            $accion = 'add comentario al servicio id ' . $id;
            $this->auditoria($accion, 'EXEQUIALES');
            return redirect()->route('exequial.prestarServicio.edit', $id)->with('success', 'Comentario agregado exitosamente.');
        } else {
            return redirect()->route('exequial.prestarServicio.edit', $id)->with('error', 'Error al agregar el comentario.');
        }
    }

    public function dashboard()
    {
        $resultado = DB::table('EXE_MAEC_EXSER as s')->join('MaeTerceros as t', 't.cod_ter', '=', 's.cedulaTitular')->join('MaeDistritos as d', 'd.COD_DIST', '=', 't.cod_dist')->select('d.COD_DIST', 'd.NOM_DIST', DB::raw('COUNT(*) as total'))->groupBy('d.COD_DIST', 'd.NOM_DIST')->orderBy('d.COD_DIST')->get();
        $arraydata = [
            'labels' => $resultado->pluck('NOM_DIST')->toArray(),
            'valores' => $resultado->pluck('total')->toArray(),
        ];
        $totalservicios = ExMonitoria::count();

        $datamunicipios = DB::table('EXE_MAEC_EXSER as s')->join('MaeMunicipios as m', 'm.id', '=', 's.municipio')->select('m.nombre as municipio', DB::raw('COUNT(*) as total'))->groupBy('m.id', 'm.nombre')->orderByDesc('total')->limit(15)->get();
        $arraydatamunicipios = [
            'labels' => $datamunicipios->pluck('municipio')->toArray(),
            'valores' => $datamunicipios->pluck('total')->toArray(),
        ];
        $total = DB::table('Auditoria')->where('area', 'EXEQUIALES')->count();
        $add = DB::table('Auditoria')->where('area', 'EXEQUIALES')->where('accion', 'like', 'add beneficiario%')->count();
        $update = DB::table('Auditoria')->where('area', 'EXEQUIALES')->where('accion', 'like', 'update beneficiario%')->count();
        $delete = DB::table('Auditoria')->where('area', 'EXEQUIALES')->where('accion', 'like', 'delete beneficiario%')->count();
        $presser = DB::table('Auditoria')->where('area', 'EXEQUIALES')->where('accion', 'like', 'prestar servicio%')->count();
        $kpis = [
            [
                'label' => 'Agregar beneficiario',
                'value' => $add,
                'percent' => $total > 0 ? round(($add / $total) * 100) : 0,
                'color' => '#39C666',
            ],
            [
                'label' => 'Actualizar beneficiario',
                'value' => $update,
                'percent' => $total > 0 ? round(($update / $total) * 100) : 0,
                'color' => '#FFA21D',
            ],
            [
                'label' => 'Eliminar beneficiario',
                'value' => $delete,
                'percent' => $total > 0 ? round(($delete / $total) * 100) : 0,
                'color' => '#EA4D4D',
            ],
            [
                'label' => 'Prestar servicio',
                'value' => $presser,
                'percent' => $total > 0 ? round(($presser / $total) * 100) : 0,
                'color' => '#3454D1',
            ],
        ];

        return view('exequial.prestarServicio.dashboard', compact('arraydata', 'totalservicios', 'arraydatamunicipios', 'kpis'));
    }
}
