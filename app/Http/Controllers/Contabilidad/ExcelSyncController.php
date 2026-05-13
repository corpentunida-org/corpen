<?php

namespace App\Http\Controllers\Contabilidad;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\TransaccionesExport;
use App\Models\Contabilidad\ConExtractoTransaccion;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ExcelSyncController extends Controller
{
    public function index()
    {
        return view('contabilidad.extractos.sincronizar');
    }

    public function descargarExcel() 
    {
        return Excel::download(new TransaccionesExport, 'Data_Transacciones.xlsx');
    }

    /**
     * PASO 1: Lee el Excel gigante y lo manda a previsualizar.
     */
    public function subirExcel(Request $request) 
    {
        $request->validate([
            'archivo_excel' => 'required|mimes:xlsx,xls|max:51200' // Hasta 50MB
        ]);

        $archivo = $request->file('archivo_excel');
        $registrosPrevia = [];
        
        // Para validación rápida: traemos todos los hashes
        $hashesExistentes = ConExtractoTransaccion::pluck('hash_transaccion')->toArray();
        $hashesLookup = array_flip($hashesExistentes);
        $hashesVistosEnArchivo = [];

        try {
            $data = Excel::toArray(new class {}, $archivo)[0];
            $isHeader = true;

            foreach ($data as $row) {
                if ($isHeader) { $isHeader = false; continue; }
                if (empty($row[0])) continue; // Salta filas sin ID

                // Orden basado en tu Export/Import original
                $idTransaccion = $row[0];
                $fechaRaw      = $row[1];
                $cedula        = $row[2] ?? '';
                $monto         = isset($row[3]) ? (float)$row[3] : 0;
                $oficina       = $row[4] ?? null;
                $idCuenta      = $row[5] ?? null;
                $estado        = $row[6] ?? 'Pendiente';

                // Conversión de Fecha
                if (is_numeric($fechaRaw)) {
                    $dt = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($fechaRaw);
                } else {
                    $dt = Carbon::parse($fechaRaw);
                }
                
                $fechaFormateada = $dt->format('YmdHis');
                $fechaVista      = $dt->format('Y-m-d\TH:i:s');

                // Recalculamos el hash por si fue editado
                $hashCalculado = "{$idCuenta}-{$fechaFormateada}-{$monto}-{$cedula}";
                
                $esDuplicado = isset($hashesLookup[$hashCalculado]) || isset($hashesVistosEnArchivo[$hashCalculado]);

                $registrosPrevia[] = [
                    'id_transaccion'          => $idTransaccion,
                    'fecha_movimiento'        => $fechaVista,
                    'referencia_cedula'       => $cedula,
                    'valor_ingreso'           => $monto,
                    'referencia_oficina'      => $oficina,
                    'id_con_cuentas_bancaria' => $idCuenta,
                    'estado_conciliacion'     => $estado,
                    'hash_transaccion'        => $hashCalculado,
                    'es_duplicado'            => $esDuplicado
                ];

                $hashesVistosEnArchivo[$hashCalculado] = true;
            }
        } catch (\Exception $e) {
            Log::error("Error leyendo Excel Sync: " . $e->getMessage());
            return back()->withErrors('Error al leer el archivo: ' . $e->getMessage());
        }

        return view('contabilidad.extractos.sincronizar', compact('registrosPrevia', 'hashesExistentes'));
    }

    /**
     * PASO 2: Guarda la información previsualizada en BD (Upsert Masivo)
     */
    public function confirmarSincronizacion(Request $request)
    {
        $datos = json_decode($request->registros_json, true);
        
        if (empty($datos)) {
            return redirect()->route('contabilidad.sincronizar.index')->withErrors('No se recibieron datos para sincronizar.');
        }

        // Lotes de 1000 para no reventar MySQL
        $lotes = array_chunk($datos, 1000);

        try {
            DB::transaction(function () use ($lotes) {
                foreach ($lotes as $lote) {
                    $cleanLote = array_map(function($item) {
                        return [
                            'id_transaccion'          => $item['id_transaccion'],
                            'fecha_movimiento'        => Carbon::parse($item['fecha_movimiento'])->format('Y-m-d H:i:s'),
                            'referencia_cedula'       => $item['referencia_cedula'],
                            'valor_ingreso'           => $item['valor_ingreso'],
                            'referencia_oficina'      => $item['referencia_oficina'],
                            'id_con_cuentas_bancaria' => $item['id_con_cuentas_bancaria'],
                            'estado_conciliacion'     => $item['estado_conciliacion'],
                            'hash_transaccion'        => $item['hash_transaccion'],
                            'updated_at'              => now() // Forzar fecha actualización
                        ];
                    }, $lote);

                    ConExtractoTransaccion::upsert($cleanLote, ['id_transaccion'], [
                        'fecha_movimiento', 'referencia_cedula', 'valor_ingreso', 
                        'referencia_oficina', 'id_con_cuentas_bancaria', 
                        'estado_conciliacion', 'hash_transaccion', 'updated_at'
                    ]);
                }
            });
            return redirect()->route('contabilidad.sincronizar.index')->with('success', count($datos) . ' registros procesados correctamente.');
        } catch (\Exception $e) {
            Log::error("Error UPSERT Sync: " . $e->getMessage());
            return back()->withErrors('Fallo en Base de Datos: ' . $e->getMessage());
        }
    }
}