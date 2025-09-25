<?php

namespace App\Http\Controllers\Seguros;

use App\Http\Controllers\Controller;
use App\Models\Maestras\maeTerceros;
use App\Models\Seguros\SegBeneficios;
use App\Models\Seguros\SegPoliza;
use App\Models\Seguros\SegBeneficiario;
use App\Models\Seguros\SegNovedades;
use App\Models\Seguros\SegTercero;
use App\Models\Seguros\SegAsegurado;
use App\Models\Seguros\SegReclamaciones;
use App\Models\Seguros\SegPlan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\AuditoriaController;
use Carbon\Carbon;
use App\Imports\ExcelImport;
use App\Imports\ExcelExport;
use Maatwebsite\Excel\Facades\Excel;
use App\Http\Controllers\Exequial\ComaeTerController;

class SegPolizaController extends Controller
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
        $update = SegAsegurado::where('parentesco', 'AF')
            ->whereHas('polizas', function ($query) {
                $query->whereNull('valorpAseguradora');
            })
            ->paginate(7);
        return view('seguros.polizas.index', compact('update'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('seguros.polizas.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $terceroontable = SegTercero::where('cedula', $request->tercedula)->exists();
        if ($terceroontable) {
            return redirect()->route('seguros.poliza.create')->with('error', 'Ya existe un tercero con la cédula ingresada');
        }
        $controllerapi = new ComaeTerController();
        $terapi = $controllerapi->show($request->tercedula);
        if ($terapi->getStatusCode() === 404) {
            return redirect()->route('seguros.poliza.create')->with('error', 'La cédula ingresada no coincide con ningún documento en SiaSoft');
        } else {
            $tercero = SegTercero::create([
                'cedula' => $request->tercedula,
                'nombre' => strtoupper($request->ternombre),
                'fechaNacimiento' => $request->fechaNacimiento,
                'telefono' => $request->tertelefono,
                'genero' => $request->tergenero,
                'distrito' => $request->terdistrito,
            ]);
            if ($request->estitular === '1') {
                $asegurado = SegAsegurado::create([
                    'cedula' => $tercero->cedula,
                    'parentesco' => 'AF',
                    'titular' => $tercero->cedula,
                    'valorpAseguradora' => $request->valorpagaraseguradora,
                ]);
            } else {
                $asegurado = SegAsegurado::create([
                    'cedula' => $tercero->cedula,
                    'parentesco' => $request->parentesco,
                    'titular' => $request->titularasegurado,
                ]);
            }
            /*$plan = SegPlan::find($request->selectPlanes);
            $valorPrimaFinal = $plan->prima_aseguradora;
            if ($request->extra_prima !== null && $request->extra_prima != 0) {
                $valorPrimaFinal += ($plan->prima_aseguradora * $request->extra_prima) / 100;
            }
            $poliza = SegPoliza::create([
                'seg_asegurado_id' => $asegurado->cedula,
                'seg_convenio_id' => substr((string) $plan->seg_convenio_id, 4),
                'active' => true,
                'fecha_inicio' => Carbon::now()->toDateString(),
                'seg_plan_id' => $request->selectPlanes,
                'valor_asegurado' => $plan->valor,
                'valor_prima' => $valorPrimaFinal,
                'extra_prima' => $request->extra_prima,
                'descuento' => $request->desval,
                'descuentopor' => $request->despor,
                'valorpagaraseguradora' => $request->valorpagaraseguradora,
            ]);
            if ($request->agregarbene) {
                SegBeneficios::create([
                    'cedulaAsegurado' => $asegurado->cedula,
                    'poliza' => $poliza->id,
                    'porcentajeDescuento' => $request->despor,
                    'valorDescuento' => $request->desval,
                ]);
            }*/

            $this->auditoria('TERCERO CREAD0 ID ' . $tercero->cedula);
            $this->auditoria('ASEGURADO CREADO ID ' . $asegurado->cedula);
            $this->auditoria('POLIZA CREADA ID ' . $poliza->id);

            return redirect()
                ->route('seguros.poliza.show', ['poliza' => 1, 'id' => $tercero->cedula])
                ->with('success', 'Se creó correctamente la póliza');
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request)
    {
        $id = $request->input('id');
        $poliza = SegPoliza::where('seg_asegurado_id', $id)
            ->with(['tercero', 'asegurado', 'asegurado.terceroAF', 'plan.condicion', 'plan.coberturas', 'esreclamacion.estadoReclamacion'])
            ->first();
        if (!$poliza) {
            return redirect()->route('seguros.poliza.index')->with('warning', 'No se encontró la cédula como asegurado de una poliza');
        }
        $titularCedula = $poliza->asegurado->titular;
        $grupoFamiliar = SegAsegurado::where('Titular', $titularCedula)->with('tercero', 'polizas.plan.coberturas')->get();

        $totalPrima = DB::table('SEG_polizas')->whereIn('seg_asegurado_id', $grupoFamiliar->pluck('cedula'))->sum('valor_prima');

        if ($poliza->asegurado->parentesco === 'AF') {
            $primaAseguradora = DB::table('SEG_polizas')->whereIn('seg_asegurado_id', $grupoFamiliar->pluck('cedula'))->sum('primapagar');
            if ($poliza->valorpagaraseguradora != $primaAseguradora) {
                $poliza->update(['valorpagaraseguradora' => $primaAseguradora]);
            }
        }

        $beneficiarios = SegBeneficiario::where('id_asegurado', $id)->where('activo', 1)->with('parentescos')->get();

        $novedades = SegNovedades::where('id_asegurado', $id)->where('id_poliza', $poliza->id)->get();
        $reclamacion = SegReclamaciones::where('cedulaAsegurado', $id)->with('cambiosEstado')->get();

        if (auth()->user()->can('seguros.poliza.valorpagar')) {
            $beneficios = SegBeneficios::where('cedulaAsegurado', $id)->where('poliza', $poliza->id)->where('active', true)->get();
        } else {
            $beneficios = collect();
        }

        $registrosnov = collect()->merge($novedades)->merge($reclamacion)->merge($beneficios)->sortBy('updated_at')->values();

        return view('seguros.polizas.show', compact('poliza', 'grupoFamiliar', 'totalPrima', 'beneficiarios', 'beneficios', 'registrosnov'));
    }

    /**
     * Show the form for editing the specified resource.
     */

    public function edit(SegPoliza $segPoliza)
    {
        return view('seguros.polizas.edit');
    }

    public function uploadCreate(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls,csv',
        ]);
        $rows = Excel::toArray(new ExcelImport(), $request->file('file'))[0];
        $updatedCount = 0;
        $failedRows = [];
        foreach ($rows as $index => $row) {
            //validar campos
            if (!in_array(strtoupper($row['genero']), ['V', 'H'])) {
                $failedRows[] = [
                    'cedula' => $row['num_doc'],
                    'obser' => 'Género inválido. Debe ser V o H.',
                ];
                continue;
            } elseif (!in_array(strtoupper($row['parentesco']), ['AF', 'CO', 'HI', 'HE'])) {
                $failedRows[] = [
                    'cedula' => $row['num_doc'],
                    'obser' => 'Parentesco inválido. Debe ser AF, CO, HI o HE.',
                ];
                continue;
            }
            $tercero = SegTercero::where('cedula', $row['num_doc'])->first();
            if (!$tercero) {
                $maeTercero = maeTerceros::where('cod_ter', $row['num_doc'])->first();
                $fechaNacimiento = Carbon::create(1899, 12, 30)->addDays($row['fecha_nac'])->toDateString();
                if (!$maeTercero) {
                    try {
                        $edad = Carbon::parse($fechaNacimiento)->age;
                        if ($edad < 0 || $edad > 120) {
                            throw new \Exception("Edad inválida: $edad años. Fuera del rango permitido.");
                        }
                        $tercero = SegTercero::create([
                            'cedula' => $row['num_doc'],
                            'nombre' => $row['nombre'],
                            'fechaNacimiento' => $fechaNacimiento,
                            'genero' => $row['genero'],
                        ]);
                        maeTerceros::create([
                            'cod_ter' => $row['num_doc'],
                            'nom_ter' => $row['nombre'],
                            'fec_nac' => $fechaNacimiento,
                            'sexo' => $row['genero'],
                        ]);
                    } catch (\Exception $e) {
                        $failedRows[] = [
                            'cedula' => $row['num_doc'],
                            'obser' => 'La fecha de nacimiento no esta en el formato correcto',
                        ];
                        continue;
                    }
                } else {
                    $maeTercero->update([
                        'nom_ter' => $row['nombre'],
                        'fec_nac' => $fechaNacimiento,
                        'sexo' => $row['genero'],
                    ]);
                    $tercero = SegTercero::create([
                        'cedula' => $maeTercero->cod_ter,
                        'nombre' => $maeTercero->nom_ter,
                        'fechaNacimiento' => $maeTercero->fec_nac,
                        'genero' => $maeTercero->sexo,
                    ]);
                }
            }
            $asegurado = SegAsegurado::where('cedula', $tercero->cedula)->first();
            if (!$asegurado) {
                $asegurado = SegAsegurado::create([
                    'cedula' => $tercero->cedula,
                    'parentesco' => $row['parentesco'],
                    'titular' => $row['titular'] ?? $tercero->cedula,
                    'valorpAseguradora' => $row['valor_titular'] ?? null,
                ]);
            }
            $poliza = SegPoliza::where('seg_asegurado_id', $asegurado->cedula)->first();
            $condicion_id = app(SegPlanController::class)->getCondicion($tercero->edad);
            $plan_id = SegPlan::select('id')->where('vigente', true)->where('condicion_corpen', $condicion_id)->where('valor', $row['valor_asegurado'])->first();
            $plan_id_value = $plan_id ? $plan_id->id : 77;
            /*if (!$plan_id) {
                $failedRows[] = [
                    'cedula' => $row['num_doc'],
                    'obser' => 'No se encontró un plan con el valor asegurado de ' . $row['valor_asegurado'],
                ];
                continue;
            }*/
            if (!$poliza) {
                $poliza = SegPoliza::create([
                    'seg_asegurado_id' => $asegurado->cedula,
                    'seg_convenio_id' => $row['poliza'],
                    'active' => true,
                    'fecha_inicio' => Carbon::now()->toDateString(),
                    'seg_plan_id' => $plan_id_value,
                    'valor_asegurado' => $row['valor_asegurado'],
                    'valor_prima' => $row['prima'],
                    'primapagar' => $row['prima_corpen'],
                    'extra_prima' => $row['extra_prim'] ?? 0,
                    'valorpagaraseguradora' => $row['valor_titular'] ?? null,
                ]);
                $updatedCount++;
                $this->auditoria('POLIZA CREADA ID ' . $poliza->id);
            } else {
                $poliza->update([
                    'fecha_inicio' => Carbon::now()->toDateString(),
                    'seg_plan_id' => $plan_id_value,
                    'valor_asegurado' => $row['valor_asegurado'],
                    'valor_prima' => $row['prima'],
                    'primapagar' => $row['prima_corpen'],
                    'extra_prima' => $row['extra_prim'] ?? 0,
                    'valorpagaraseguradora' => $row['valor_titular'] ?? null,
                ]);
                $updatedCount++;
                /*$failedRows[] = [
                    'cedula' => $row['num_doc'],
                    'obser' => 'Ya existe una póliza para este asegurado.',
                ];*/
            }
        }
        return redirect()
            ->route('seguros.poliza.viewupload')
            ->with('success', 'Se actualizaron exitosamente ' . $updatedCount . ' registros')
            ->with('failedRows', $failedRows);
    }

    public function upload(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls,csv',
        ]);

        $file = $request->file('file');
        $import = new ExcelImport();
        $rows = Excel::toArray(new ExcelImport(), $request->file('file'))[0];

        $updatedCount = 0;
        $failedRows = [];
        foreach ($rows as $index => $row) {
            $modeloData = SegPoliza::where('seg_asegurado_id', $row['cod_ter'])->first();
            if ($modeloData) {
                SegNovedades::create([
                    'id_asegurado' => $modeloData->seg_asegurado_id,
                    'id_poliza' => $modeloData->id,
                    'valorpagar' => $row['deb_mov'],
                    'valorPrimaPlan' => $modeloData->valor_prima,
                    'plan' => $modeloData->seg_plan_id,
                    'fechaNovedad' => Carbon::now()->toDateString(),
                    'valorAsegurado' => $modeloData->valor_asegurado,
                    'observaciones' => $request->observacion,
                ]);
                $modeloData->valorpagaraseguradora = $row['deb_mov'];
                $modeloData->save();
                $updatedCount++;
            } else {
                $failedRows[] = $row;
            }
        }
        $this->auditoria('actualizar valor a pagar polizas con carga excel');
        return view('seguros.polizas.edit', [
            'success' => 'Se actualizaron exitosamente ' . $updatedCount . ' registros',
            'failedRows' => $failedRows,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, SegPoliza $segPoliza) {}

    public function exportarFormato()
    {
        $headings = ['POLIZA', 'TIP_DOC', 'NUM_DOC', 'NOMBRE', 'GENERO', 'FECHA_NAC', 'PARENTESCO', 'TITULAR', 'VALOR_ASEGURADO', 'EXTRA PRIM', 'PRIMA', 'PRIMA_CORPEN', 'VALOR_TITULAR'];
        return Excel::download(new ExcelExport([], $headings), 'POLIZAS.xlsx');
    }

    public function destroy($poliza, Request $request)
    {
        /*SegNovedades::create([
            'id_asegurado' => $request->input('aseguradoid'),
            'id_poliza' => $poliza,
            'fechaNovedad' => $now->toDateString(),
            'retiro' => true,
            'observaciones' => $request->input('observacionretiro'),
        ]);*/

        SegNovedades::create([
            'id_asegurado' => $request->input('aseguradoid'),
            'id_poliza' => $poliza,
            'estado' => 3,
            'id_plan' => true,
            'tipo' => 3,
        ]);

        SegPoliza::where('id', $poliza)->update(['active' => false]);
        $accion = 'cancelar poliza id  ' . $poliza;
        $this->auditoria($accion);
        return redirect()->route('seguros.poliza.index')->with('success', 'Se canceló el plan correctamente');
    }

    public function namesearch(Request $request)
    {
        $name = str_replace(' ', '%', $request->input('id'));
        $asegurados = SegAsegurado::with(['tercero', 'polizas'])
            ->whereHas('tercero', function ($query) use ($name) {
                $query->where('nom_ter', 'like', '%' . $name . '%');
            })
            ->get();
        return view('seguros.polizas.search', compact('asegurados'));
    }

    public function exportcxc()
    {
        $datos = SegPoliza::where('active', true)
            ->with(['tercero', 'asegurado'])
            ->get();
        $headings = ['POLIZA', 'ID', 'NOMBRE', 'NUM DOC', 'FECHA NAC', 'GENERO', 'EDAD', 'DOC AF', 'PARENTESCO', 'FEC NOVEDAD', 'VALOR ASEGURADO', 'EXTRA PRIMA', 'PRIMA'];
        $datosFormateados = $datos->map(function ($item) {
            $fechaNacimiento = Carbon::parse($item->fecha_nacimiento);
            return [
                'poliza' => $item->seg_convenio_id,
                'id' => $item->id,
                'nombre' => $item->nombre_tercero ?? ' ',
                'num_doc' => $item->seg_asegurado_id,
                'fecha_nac' => $fechaNacimiento,
                'genero' => $item->genero ?? '',
                'edad' => $fechaNacimiento->age ?? '0',
                'doc_af' => $item->asegurado->titular ?? '',
                'parentesco' => $item->asegurado->parentesco ?? ' ',
                'fec_novedad' => $item->fecha_novedad,
                'valor_asegurado' => $item->valor_asegurado,
                'extra_prima' => $item->extra_prima,
                'prima' => $item->valor_prima,
            ];
        });
        return Excel::download(new ExcelExport($datosFormateados, $headings), 'DATOS SEGUROS VIDA.xlsx');
    }
}
