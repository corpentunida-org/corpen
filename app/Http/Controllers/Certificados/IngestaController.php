<?php

namespace App\Http\Controllers\Certificados;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Facades\Excel;

// AUDITORIA
use App\Models\Certificados\CarSiaOperacionLog;
use App\Models\Certificados\CarSiaOrigenEvento;
use App\Models\Certificados\CarSiaEventoAuditoria;

// MODELOS
use App\Models\Certificados\CarSiaApi;
use App\Models\Certificados\CarSiaOperacion;
use App\Models\Certificados\CarSiaEstado;
use App\Models\Certificados\CarSiaEstadoOperacion;
use App\Models\Creditos\LineaCredito;

class IngestaController extends Controller
{
    /**
     * =========================================================================
     * 1. LEE LOTES CRUDOS Y APLICA FILTROS POR BLOQUE ESPECÍFICO
     * =========================================================================
     */
    public function index(Request $request)
    {
        try {
            // 1. Obtener todos los bloques ordenados matemáticamente con Colecciones (DB Agnostic)
            $bloquesDisponibles = CarSiaApi::whereNotNull('numero_bloque')
                ->distinct()
                ->pluck('numero_bloque')
                ->map(function ($b) { return (int) $b; })
                ->unique()
                ->sortDesc()
                ->values();

            $bloqueActivo = $request->input('bloque', $bloquesDisponibles->first());

            $query = CarSiaApi::query();

            // 2. AISLAMIENTO TOTAL: Filtramos TODO por el Bloque Activo
            if ($bloqueActivo) {
                $query->where('numero_bloque', $bloqueActivo);
            } else {
                $query->where('id', 0); // Si no hay data, forzamos vacío para evitar mostrar basura
            }

            if ($request->filled('buscar_cedula')) {
                $termino = trim($request->buscar_cedula);
                $query->where(function($q) use ($termino) {
                    $q->where('tercero', 'LIKE', $termino . '%')
                      ->orWhere('id_factura', 'LIKE', $termino . '%');
                });
            }
            // FILTRO POR ESTADO
            if ($request->filled('estado')) {
                $query->where('estado', $request->estado);
            }

            // 3. LA MAGIA: Forzamos a que los 'PENDIENTE' siempre salgan primero
            $lotesCrudos = $query->orderByRaw("CASE WHEN estado = 'PENDIENTE' THEN 1 ELSE 2 END")
                                 ->orderBy('fecha_ad', 'desc')
                                 ->paginate(5);

            // 4. CACHÉ DE KPIs AISLADO POR BLOQUE
            $kpiCacheKey = "kpis_ingesta_staging_bloque_{$bloqueActivo}";
            $kpi = Cache::remember($kpiCacheKey, 60, function () use ($bloqueActivo) {
                if (!$bloqueActivo) {
                    return ['total_registros' => 0, 'procesados' => 0, 'anulados' => 0, 'pendientes' => 0, 'valor_pendiente' => 0];
                }

                $totalesGlobales = CarSiaApi::where('numero_bloque', $bloqueActivo)
                    ->selectRaw('
                        COUNT(id) as total_registros,
                        SUM(CASE WHEN estado = "PROCESADO" THEN 1 ELSE 0 END) as procesados,
                        SUM(CASE WHEN anular = 1 THEN 1 ELSE 0 END) as anulados
                    ')->first();

                $totalesPendientes = CarSiaApi::where('numero_bloque', $bloqueActivo)
                    ->where('estado', '!=', 'PROCESADO')
                    ->where(function($q) {
                        $q->whereNull('anular')->orWhere('anular', '!=', 1);
                    })
                    ->selectRaw('COUNT(id) as pendientes, SUM(valor) as valor_pendiente')
                    ->first();

                return [
                    'total_registros' => $totalesGlobales->total_registros ?? 0,
                    'procesados'      => $totalesGlobales->procesados ?? 0,
                    'anulados'        => $totalesGlobales->anulados ?? 0,
                    'pendientes'      => $totalesPendientes->pendientes ?? 0,
                    'valor_pendiente' => $totalesPendientes->valor_pendiente ?? 0,
                ];
            });

            $totalPendientes = $kpi['pendientes'];
            $estados = CarSiaEstado::all();
            $tipos = DB::table('car_sia_tipos')->get();

            return view('certificados.ingesta.index', compact('lotesCrudos', 'totalPendientes', 'estados', 'tipos', 'kpi', 'bloquesDisponibles', 'bloqueActivo'));

        } catch (\Exception $e) {
            Log::error('CERTIFICADOS Ingesta - Error al cargar staging: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Ocurrió un error al procesar los datos.');
        }
    }

    /**
     * =========================================================================
     * 2. CARGAR EXCEL (ASIGNACIÓN DE NÚMERO DE BLOQUE BLINDADA)
     * =========================================================================
     */
    public function cargarExcel(Request $request)
    {
        $request->validate([
            'archivo_excel' => 'required|mimes:xlsx,xls,csv|max:20480'
        ]);

        try {
            ini_set('max_execution_time', 300);
            ini_set('memory_limit', '512M');

            $datosExcel = Excel::toArray(new \stdClass(), $request->file('archivo_excel'));
            $hoja = $datosExcel[0];

            if (count($hoja) < 2) {
                return redirect()->back()->with('error', 'El archivo está vacío o no tiene registros.');
            }

            $encabezados = array_map(function($columna) {
                return trim(strtolower(preg_replace('/\s+/', ' ', $columna)));
            }, $hoja[0]);

            array_shift($hoja); // Quitar encabezados

            $mapaColumnas = [
                'id factura'     => 'id_factura',
                'tercero'        => 'tercero',
                'nombre tercero' => 'nombre_tercero',
                'valor'          => 'valor',
                'fecha venc.'    => 'fecha_venci',
                '# documento'    => 'numero_documento',
                'año'            => 'anio',
                'mes'            => 'mes',
                'cuenta'         => 'cuenta',
                'banco'          => 'banco'
            ];

            $ahora = now()->format('Y-m-d H:i:s');

            // 1. SOLUCIÓN CRÍTICA: Bloque Máximo Global (Infalible)
            // Revisamos ambas tablas y tomamos el valor numérico más alto, luego le sumamos 1.
            $maxStaging = CarSiaApi::max('numero_bloque') ?? 0;
            $maxOperacion = CarSiaOperacion::max('numero_bloque') ?? 0;
            $nuevoBloque = max((int)$maxStaging, (int)$maxOperacion) + 1;

            DB::transaction(function () use ($hoja, $encabezados, $mapaColumnas, $ahora, $nuevoBloque) {

                $loteInsercionMasiva = [];

                foreach ($hoja as $fila) {
                    if (empty(array_filter($fila, function($value) { return $value !== null && $value !== ''; }))) {
                        continue;
                    }

                    $datosInsertar = [
                        'estado'        => 'PENDIENTE',
                        'fecha_ad'      => $ahora,
                        'created_at'    => $ahora,
                        'updated_at'    => $ahora,
                        'numero_bloque' => $nuevoBloque, // Inyección del bloque invulnerable
                    ];

                    foreach ($mapaColumnas as $columnaExcel => $campoBD) {
                        $indiceColumna = array_search($columnaExcel, $encabezados);

                        if ($indiceColumna !== false && array_key_exists($indiceColumna, $fila)) {
                            $valorCelda = $fila[$indiceColumna];

                            if ($campoBD === 'valor' && $valorCelda !== null) {
                                $valorCelda = preg_replace('/[^0-9.-]/', '', (string)$valorCelda);
                                $valorCelda = $valorCelda === '' ? 0 : (float)$valorCelda;
                            }

                            if ($campoBD === 'fecha_venci' && is_numeric($valorCelda)) {
                                try {
                                    $valorCelda = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($valorCelda)->format('Y-m-d');
                                } catch (\Exception $e) {
                                    $valorCelda = null;
                                }
                            }

                            $datosInsertar[$campoBD] = $valorCelda;
                        }
                    }
                    $loteInsercionMasiva[] = $datosInsertar;
                }

                // Chunk optimizado
                $bloques = array_chunk($loteInsercionMasiva, 1000);
                foreach ($bloques as $bloque) {
                    CarSiaApi::insert($bloque);
                }
            });

            return redirect()->route('certificados.ingesta.index', ['bloque' => $nuevoBloque])
                             ->with('success', "Archivo cargado exitosamente. Asignado al Lote #{$nuevoBloque}");

        } catch (\Exception $e) {
            Log::error('CERTIFICADOS Ingesta - Error masivo Excel: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Fallo técnico leyendo el Excel: ' . $e->getMessage());
        }
    }

    /**
     * =========================================================================
     * 3. MOTOR DE INYECCIÓN (ALTO RENDIMIENTO - OPTIMIZADO POR BLOQUE)
     * =========================================================================
     */
    public function inyectarBloques(Request $request)
    {
        $request->validate([
            'id_car_sia_estados' => 'required|integer',
            'id_car_sia_tipos'   => 'required|integer',
            'bloque_origen'      => 'required|integer',
        ]);

        try {
            ini_set('max_execution_time', 600);
            ini_set('memory_limit', '512M');

            $bloqueOrigen = $request->bloque_origen;
            $clientesProcesados = 0;
            $cedulasIgnoradas = 0;

            DB::transaction(function () use (&$clientesProcesados, &$cedulasIgnoradas, $request, $bloqueOrigen) {

                // 1. Obtenemos todas las cédulas que el Excel quiere procesar
                $cedulasPendientes = CarSiaApi::where('numero_bloque', $bloqueOrigen)
                    ->where('estado', 'PENDIENTE')
                    ->where(function ($query) {
                        $query->whereNull('anular')->orWhere('anular', '!=', 1);
                    })
                    ->distinct()
                    ->pluck('tercero')
                    ->toArray();

                if (empty($cedulasPendientes)) {
                    return;
                }

                // 2. EL PORTERO: Buscamos cuáles de esas cédulas REALMENTE existen en MaeTerceros
                $cedulasValidas = DB::table('MaeTerceros')
                    ->whereIn('cod_ter', $cedulasPendientes)
                    ->pluck('cod_ter')
                    ->toArray();

                $cedulasIgnoradas = count($cedulasPendientes) - count($cedulasValidas);

                if (empty($cedulasValidas)) {
                    throw new \Exception('Ninguna de las cédulas del lote existe en el maestro de terceros (MaeTerceros).');
                }

                $origen = CarSiaOrigenEvento::firstOrCreate(['nombre' => 'Interfaz Web']);
                $evento = CarSiaEventoAuditoria::firstOrCreate(['nombre' => 'Inyección Masiva ERP']);

                $anioActual = date('Y');
                $cantidadActual = CarSiaOperacion::whereYear('created_at', $anioActual)->count();

                $idEstadoReal = $request->id_car_sia_estados;
                $idTipoEvento = $request->id_car_sia_tipos;
                $numeroBloqueNuevo = $bloqueOrigen;

                $ahora = now()->format('Y-m-d H:i:s');

                // 3. PREPARACIÓN EN MEMORIA (SOLO LAS CÉDULAS VÁLIDAS)
                $operacionesMatriz = [];
                foreach ($cedulasValidas as $cedula) {
                    $cantidadActual++;
                    $consecutivo = str_pad($cantidadActual, 4, '0', STR_PAD_LEFT);
                    $numeroRadicado = "INI-{$anioActual}-{$consecutivo}";

                    $operacionesMatriz[] = [
                        'numero_radicado' => $numeroRadicado,
                        'numero_bloque'   => $numeroBloqueNuevo,
                        'id_tercero'      => $cedula,
                        'created_at'      => $ahora,
                        'updated_at'      => $ahora,
                    ];
                    $clientesProcesados++;
                }

                // 4. INSERCIÓN MASIVA DE MATRIZ
                foreach (array_chunk($operacionesMatriz, 1000) as $bloque) {
                    CarSiaOperacion::insert($bloque);
                }

                // =========================================================
                // 5. INSERCIÓN DE PIVOTES (UN SOLO REGISTRO POR BLOQUE)
                // =========================================================

                // Un solo registro para el Tipo de Operación
                DB::table('car_sia_tipos_operacion')->insert([
                    'id_car_sia_operaciones' => null, // Queda en blanco, asocia al bloque general
                    'id_car_sia_tipos'       => $idTipoEvento,
                    'numero_bloque'          => $numeroBloqueNuevo,
                    'created_at'             => $ahora,
                    'updated_at'             => $ahora,
                ]);

                // Un solo registro para el Estado de la Operación
                DB::table('car_sia_estados_operacion')->insert([
                    'id_car_sia_operaciones' => null, // Queda en blanco, asocia al bloque general
                    'id_car_sia_estados'     => $idEstadoReal,
                    'numero_bloque'          => $numeroBloqueNuevo,
                    'created_at'             => $ahora,
                    'updated_at'             => $ahora,
                ]);
                // =========================================================

                // 6. ACTUALIZACIÓN MASIVA SOLO A LOS CLIENTES VÁLIDOS PROCESADOS
                CarSiaApi::where('numero_bloque', $bloqueOrigen)
                    ->where('estado', 'PENDIENTE')
                    ->whereIn('tercero', $cedulasValidas)
                    ->where(function ($query) {
                        $query->whereNull('anular')->orWhere('anular', '!=', 1);
                    })
                    ->update([
                        'estado'     => 'PROCESADO',
                        'updated_at' => $ahora
                    ]);

                // 7. LOG DE AUDITORÍA
                try {
                    CarSiaOperacionLog::create([
                        'numero_bloque'                 => $numeroBloqueNuevo,
                        'id_car_sia_operaciones_lineas' => null,
                        'id_car_sia_origenes_evento'    => $origen->id,
                        'id_car_sia_eventos_auditoria'  => $evento->id,
                        'id_user'                       => Auth::check() ? Auth::id() : null,
                        'ip'                            => $request->ip() ?? '127.0.0.1',
                        'detalles_ejecucion'            => [
                            'accion'             => 'Generacion Automatica Operacion Lote (Validada contra Maestro)',
                            'bloque_origen'      => $bloqueOrigen,
                            'clientes_procesados'=> $clientesProcesados,
                            'clientes_ignorados' => $cedulasIgnoradas,
                            'estado_asignado'    => $idEstadoReal,
                            'tipo_asignado'      => $idTipoEvento
                        ]
                    ]);
                } catch (\Exception $exLog) {
                    Log::warning("Fallo log de auditoría: " . $exLog->getMessage());
                }
            });

            if ($clientesProcesados === 0 && $cedulasIgnoradas === 0) {
                if ($request->ajax()) return response()->json(['error' => 'No hay lotes válidos.'], 400);
                return redirect()->back()->with('error', 'No se encontraron lotes pendientes.');
            }

            // Mensajes para el usuario final
            session()->flash('inyeccion_exitosa', true);
            session()->flash('resumen_clientes', $clientesProcesados);

            if ($cedulasIgnoradas > 0) {
                session()->flash('warning', "ATENCIÓN: Se omitieron {$cedulasIgnoradas} clientes porque su cédula no existe en el sistema.");
            }

            Cache::forget("kpis_ingesta_staging_bloque_{$bloqueOrigen}");

            if ($request->ajax()) return response()->json(['success' => true]);

            return redirect()->back();

        } catch (\Exception $e) {
            Log::error('CERTIFICADOS Ingesta - Error crítico: ' . $e->getMessage());
            if ($request->ajax()) return response()->json(['error' => 'Error SQL: ' . $e->getMessage()], 500);
            return redirect()->back()->with('error', 'Error SQL: ' . $e->getMessage());
        }
    }

    /**
     * =========================================================================
     * 4. ANULAR LOTE
     * =========================================================================
     */
    public function anularLote(int $id)
    {
        try {
            $lote = CarSiaApi::findOrFail($id);
            $lote->update(['anular' => 1]);

            Cache::forget("kpis_ingesta_staging_bloque_{$lote->numero_bloque}");

            return redirect()->back()->with('success', 'El lote fue anulado.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'No se pudo anular el registro.');
        }
    }
}
