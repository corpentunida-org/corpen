<?php

namespace App\Http\Controllers\Seguros;

use App\Http\Controllers\Controller;
use App\Models\Seguros\SegBeneficios;
use App\Models\Seguros\SegPoliza;
use App\Models\Seguros\SegBeneficiario;
use App\Models\Seguros\SegNovedades;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\AuditoriaController;
use Carbon\Carbon;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\ExcelImport;
use App\Imports\ExcelExport; 

use App\Models\Seguros\SegAsegurado;

class SegPolizaController extends Controller
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
        $update = SegAsegurado::where('parentesco', 'AF')
        ->whereHas('polizas', function($query) {$query->whereNull('valorpagaraseguradora')
              ->orWhere('valorpagaraseguradora', ' ');})->get();
              
        return view('seguros.polizas.index', compact('update'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request)
    {
        $id = $request->input('id');
        $poliza = SegPoliza::where('seg_asegurado_id', $id)
            ->with(['tercero', 'asegurado', 'asegurado.terceroAF', 'plan.condicion', 'plan.coberturas', 'esreclamacion.estadoReclamacion'])->first();
        if (!$poliza) {
            return redirect()->route('seguros.poliza.index')->with('warning', 'No se encontró la cédula como asegurado de una poliza');
        }
        
        $titularCedula = $poliza->asegurado->titular;
        $grupoFamiliar = SegAsegurado::where('Titular', $titularCedula)->with('tercero', 'polizas.plan.coberturas')->get();
        
        $totalPrima = DB::table('SEG_polizas')
            ->whereIn('seg_asegurado_id', $grupoFamiliar->pluck('cedula'))
            ->sum('valor_prima');

        $beneficiarios = SegBeneficiario::where('id_asegurado', $id)->get();

        $novedades = SegNovedades::where('id_asegurado', $id)
            ->where('id_poliza', $poliza->id)->get();
        $beneficios = SegBeneficios::where('cedulaAsegurado', $id)
            ->where('poliza', $poliza->id)->get();
            $registrosnov = $novedades->merge($beneficios);
            $registrosnov = $registrosnov->sortBy('created_at');
        
        return view('seguros.polizas.show', compact('poliza', 'grupoFamiliar', 'totalPrima', 'beneficiarios', 'beneficios','registrosnov'));
    }

    /**
     * Show the form for editing the specified resource.
     */

    public function edit(SegPoliza $segPoliza)
    {
        return view('seguros.polizas.edit');
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
            if ($index == 0) {
                continue;
            }            
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
        $this->auditoria("actualizar valor a pagar polizas con carga excel");
        return view('seguros.polizas.edit', compact('failedRows'))->with('success', 'Se actualizaron exitosamente ' . $updatedCount . ' registros');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, SegPoliza $segPoliza)
    {

    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($poliza, Request $request)
    {
        $now = Carbon::now();
        SegNovedades::create([
            'id_asegurado' => $request->input('aseguradoid'),
            'id_poliza' => $poliza,
            'fechaNovedad' => $now->toDateString(),
            'retiro' => true,
            'observaciones' => $request->input('observacionretiro'),
        ]);
        SegPoliza::where('id', $poliza)->update(['active' => false]);
        $accion = "cancelar poliza id  " . $poliza;
            $this->auditoria($accion); 
        return redirect()->route('seguros.novedades.index')->with('success', 'Novedad registrada correctamente');
    }

    public function namesearch(Request $request)
    {
        $name = str_replace(' ', '%', $request->input('id'));
        $asegurados = SegAsegurado::with('tercero') // Cargar la relación 'tercero'
            ->whereHas('tercero', function ($query) use ($name) {
                $query->where('nombre', 'like', '%' . $name . '%');})->get();
        return view('seguros.polizas.search', compact('asegurados'));
    }

    public function exportcxc()
    {
        $datos = SegPoliza::where('active', true)->with(['tercero', 'asegurado'])->get();
        $headings = [
            'POLIZA', 'ID', 'NOMBRE', 'NUM DOC', 'FECHA NAC', 'GENERO', 'EDAD', 'DOC AF','PARENTESCO', 'FEC NOVEDAD', 'VALOR ASEGURADO','EXTRA PRIMA', 'PRIMA'
        ];
        $datosFormateados = $datos->map(function ($item) {
        $fechaNacimiento = Carbon::parse($item->tercero?->fecha_nacimiento);
            return [
                'poliza' => $item->seg_convenio_id,
                'id' => $item->id,
                'nombre' => $item->tercero->nombre ?? ' ',
                'num_doc' => $item->seg_asegurado_id,
                'fecha_nac' => $fechaNacimiento,
                'genero' => $item->tercero->genero ?? '',
                'edad' => $fechaNacimiento->age ?? '0',
                'doc_af' => $item->asegurado->titular ?? '',
                'parentesco' => $item->asegurado->parentesco ?? ' ',
                'fec_novedad' => $item->fecha_novedad,
                'valor_asegurado' => $item->valor_asegurado, 
                'extra_prima' => $item->extra_prima, 
                'prima' => $item->valor_prima 
            ];
        });
        return Excel::download(new ExcelExport($datosFormateados,$headings), 'DATOS SEGUROS VIDA.xlsx');
    }
}
