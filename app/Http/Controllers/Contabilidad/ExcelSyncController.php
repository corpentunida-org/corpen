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
     * PASO 1: Lee el Excel masivo y lo envía a la vista para previsualización interactiva.
     */
    public function subirExcel(Request $request) 
    {
        // Forzamos al servidor a otorgar recursos máximos para procesar los 100k visuales
        ini_set('memory_limit', '2048M');
        set_time_limit(600); // 10 minutos de procesamiento máximo

        $request->validate([
            'archivo_excel' => 'required|mimes:xlsx,xls|max:61440' // Hasta 60MB
        ]);

        $archivo = $request->file('archivo_excel');
        $registrosPrevia = [];
        
        // Optimización O(1): Traemos los hashes existentes indexados por clave para máxima velocidad
        $hashesExistentes = ConExtractoTransaccion::pluck('hash_transaccion')->toArray();
        $hashesLookup = array_flip($hashesExistentes);
        $hashesVistosEnArchivo = [];

        try {
            $data = Excel::toArray(new class {}, $archivo)[0];
            $isHeader = true;

            foreach ($data as $row) {
                if ($isHeader) { 
                    $isHeader = false; 
                    continue; 
                }
                
                if (empty($row[0])) {
                    continue; // Salta filas vacías o sin ID de transacción
                }

                $idTransaccion = $row[0];
                $fechaRaw      = $row[1];
                $cedula        = $row[2] ?? '';
                $monto         = isset($row[3]) ? (float)$row[3] : 0;
                $oficina       = $row[4] ?? null;
                $idCuenta      = $row[5] ?? null;
                $estado        = $row[6] ?? 'Pendiente';

                // Conversión de Fecha Segura
                if (is_numeric($fechaRaw)) {
                    $dt = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($fechaRaw);
                } else {
                    $dt = Carbon::parse($fechaRaw);
                }
                
                $fechaFormateada = $dt->format('YmdHis');
                $fechaVista      = $dt->format('Y-m-d\TH:i:s');

                // Recalculamos el hash de control único
                $hashCalculado = "{$idCuenta}-{$fechaFormateada}-{$monto}-{$cedula}";
                
                // Validación rápida de duplicados contra BD y contra el propio archivo
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

            // Liberar memoria intermedia antes de renderizar la vista
            unset($data);
            unset($hashesVistosEnArchivo);

        } catch (\Exception $e) {
            Log::error("Error leyendo Excel Sync Masivo: " . $e->getMessage());
            return redirect()->route('contabilidad.sincronizar.index')
                ->withErrors('Error al leer el archivo Excel: ' . $e->getMessage());
        }

        return view('contabilidad.extractos.sincronizar', compact('registrosPrevia', 'hashesExistentes'));
    }

    /**
     * PASO 2: Toma la colección JSON modificada desde la vista y ejecuta el Upsert en BD.
     */
    public function confirmarSincronizacion(Request $request)
    {
        // Ampliamos límites para procesar el gigantesco string JSON enviado desde el cliente
        ini_set('memory_limit', '2048M');
        set_time_limit(600);

        $datos = json_decode($request->registros_json, true);
        
        if (empty($datos)) {
            return redirect()->route('contabilidad.sincronizar.index')
                ->withErrors('No se recibieron datos en el lote de sincronización.');
        }

        // Lotes de 1000 para no agotar los buffers de enlace de MySQL/AWS
        $lotes = array_chunk($datos, 1000);
        unset($datos); // Liberamos la variable base inmediatamente de la memoria

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
                            'updated_at'              => now(),
                            'created_at'              => now()
                        ];
                    }, $lote);

                    ConExtractoTransaccion::upsert($cleanLote, ['id_transaccion'], [
                        'fecha_movimiento', 'referencia_cedula', 'valor_ingreso', 
                        'referencia_oficina', 'id_con_cuentas_bancaria', 
                        'estado_conciliacion', 'hash_transaccion', 'updated_at'
                    ]);
                }
            });

            return redirect()->route('contabilidad.sincronizar.index')
                ->with('success', 'La sincronización de la base de datos se ha completado correctamente.');

        } catch (\Exception $e) {
            Log::error("Error crítico en el UPSERT de Sincronización: " . $e->getMessage());
            return redirect()->route('contabilidad.sincronizar.index')
                ->withErrors('Fallo en la base de datos al guardar los cambios: ' . $e->getMessage());
        }
    }
}