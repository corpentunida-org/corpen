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
use App\Models\Certificados\CarSiaPeriodo;
use App\Models\Certificados\CarSiaBloque;
use App\Models\Certificados\CarSiaEstadoOperacion;
use App\Models\Creditos\LineaCredito;

//MAESTRA DE TERCEROS
use App\Models\Maestras\MaeTerceros;

//INGESTA
use App\Imports\Certificados\IngestaExcelImport;

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

            // EL CAMBIO: Ya no forzamos el primer bloque. Si no viene en la URL, queda en null.
            $bloqueActivo = $request->input('bloque');

            $query = CarSiaApi::query();

            // 2. AISLAMIENTO TOTAL: Filtramos TODO por el Bloque Activo
            if ($bloqueActivo) {
                $query->where('numero_bloque', $bloqueActivo);
            } else {
                $query->where('id', 0); // Si no hay bloque activo, forzamos vacío para mostrar todo en 0
            }

            if ($request->filled('buscar_cedula')) {
                $termino = trim($request->buscar_cedula);
                $query->where(function($q) use ($termino) {
                    $q->where('tercero', 'LIKE', $termino . '%')
                      ->orWhere('id_factura', 'LIKE', $termino . '%');
                });
            }

            $periodos = CarSiaPeriodo::orderBy('anio', 'desc')->orderBy('mes', 'desc')->get();

            // FILTRO POR ESTADO
            if ($request->filled('estado')) {
                if ($request->estado == 'ANULADO') {
                    $query->where('anular', 1);
                } elseif ($request->estado == 'PENDIENTE') {
                    $query->where('estado', '!=', 'PROCESADO')
                          ->where(function($q) {
                              $q->whereNull('anular')->orWhere('anular', '!=', 1);
                          });
                } else {
                    $query->where('estado', $request->estado)
                          ->where(function($q) {
                              $q->whereNull('anular')->orWhere('anular', '!=', 1);
                          });
                }
            }

            // 3. LA MAGIA: Forzamos a que los 'PENDIENTE' siempre salgan primero
            $lotesCrudos = $query->orderByRaw("CASE WHEN estado = 'PENDIENTE' THEN 1 ELSE 2 END")
                                 ->orderBy('fecha_ad', 'desc')
                                 ->paginate(5);

            // 4. CACHÉ DE KPIs AISLADO POR BLOQUE
            $kpiCacheKey = "kpis_ingesta_staging_bloque_{$bloqueActivo}";
            $kpi = Cache::remember($kpiCacheKey, 60, function () use ($bloqueActivo) {

                // Si no hay bloque activo, retornamos la matriz en 0 inmediatamente
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

            return view('certificados.ingesta.index', compact('lotesCrudos', 'totalPendientes', 'estados', 'tipos', 'kpi', 'bloquesDisponibles', 'bloqueActivo', 'periodos'));

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
    /* public function cargarExcel(Request $request)
    {
        // 1. Validar que el archivo y el periodo sean enviados
        $request->validate([
            'archivo_excel' => 'required|mimes:xlsx,xls,csv|max:20480',
            'id_periodo'    => 'required|integer|exists:car_sia_periodos,id'
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

            // 2. SOLUCIÓN CRÍTICA: Bloque Máximo Global (Añadimos tu nueva tabla a la verificación)
            $maxStaging = CarSiaApi::max('numero_bloque') ?? 0;
            $maxOperacion = CarSiaOperacion::max('numero_bloque') ?? 0;
            $maxRelacional = CarSiaBloque::max('numero_bloque') ?? 0;
            $nuevoBloque = max((int)$maxStaging, (int)$maxOperacion, (int)$maxRelacional) + 1;

            DB::transaction(function () use ($hoja, $encabezados, $mapaColumnas, $ahora, $nuevoBloque, $request) {

                // 3. INTEGRACIÓN DEL MODELO: Registramos el bloque y lo asociamos al periodo
                CarSiaBloque::create([
                    'numero_bloque' => $nuevoBloque,
                    'id_periodo'    => $request->id_periodo,
                    'descripcion'   => 'Lote de carga masiva #' . $nuevoBloque,
                    'estado'        => 'PENDIENTE'
                ]);

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
                        'numero_bloque' => $nuevoBloque,
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
    } */
    public function cargarExcel(Request $request)
    {
        $request->validate([
            'archivo_excel' => 'required|mimes:xlsx,xls,csv|max:20480',
            'id_periodo'    => 'required|integer|exists:car_sia_periodos,id'
        ]);

        try {
            // 1. Guardar el archivo físicamente en storage temporal
            // Se guardará en storage/app/ingestas_temporales/
            $rutaArchivo = $request->file('archivo_excel')->store('ingestas_temporales');

            // 2. Calcular el número de bloque
            $maxStaging = CarSiaApi::max('numero_bloque') ?? 0;
            $maxOperacion = CarSiaOperacion::max('numero_bloque') ?? 0;
            $maxRelacional = CarSiaBloque::max('numero_bloque') ?? 0;
            $nuevoBloque = max((int)$maxStaging, (int)$maxOperacion, (int)$maxRelacional) + 1;

            // 3. Crear el bloque padre inmediatamente
            CarSiaBloque::create([
                'numero_bloque' => $nuevoBloque,
                'id_periodo'    => $request->id_periodo,
                'descripcion'   => 'Lote de carga masiva #' . $nuevoBloque,
                'estado'        => 'PENDIENTE'
            ]);

            // 4. Encolar el procesamiento del Excel (Esto dispara la lectura por chunks en background)
            Excel::queueImport(new IngestaExcelImport($nuevoBloque), $rutaArchivo);

            // 5. Retornar vista instantáneamente sin esperar que termine el Excel
            return redirect()->route('certificados.ingesta.index', ['bloque' => $nuevoBloque])
                            ->with('success', "Archivo recibido. Los registros del Lote #{$nuevoBloque} se están procesando en segundo plano. Refresca la página en unos momentos.");

        } catch (\Exception $e) {
            Log::error('CERTIFICADOS Ingesta - Error encolando Excel: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Fallo técnico preparando el archivo: ' . $e->getMessage());
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
            $registrosIgnorados = 0; // Cambiamos el nombre de la variable para mayor claridad

            DB::transaction(function () use (&$clientesProcesados, &$registrosIgnorados, $request, $bloqueOrigen) {

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

                // NUEVO CÁLCULO: Contamos cuántos REGISTROS (filas de facturas) se quedaron por fuera
                $registrosIgnorados = CarSiaApi::where('numero_bloque', $bloqueOrigen)
                    ->where('estado', 'PENDIENTE')
                    ->where(function ($query) {
                        $query->whereNull('anular')->orWhere('anular', '!=', 1);
                    })
                    ->whereNotIn('tercero', $cedulasValidas)
                    ->count();

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
                // 5. INSERCIÓN DE PIVOTES (SOLO SI EL BLOQUE ES NUEVO)
                // =========================================================

                $existeTipo = DB::table('car_sia_tipos_operacion')
                                ->where('numero_bloque', $numeroBloqueNuevo)
                                ->exists();

                if (!$existeTipo) {
                    // Un solo registro para el Tipo de Operación
                    DB::table('car_sia_tipos_operacion')->insert([
                        'id_car_sia_operaciones' => null,
                        'id_car_sia_tipos'       => $idTipoEvento,
                        'numero_bloque'          => $numeroBloqueNuevo,
                        'created_at'             => $ahora,
                        'updated_at'             => $ahora,
                    ]);
                }

                $existeEstado = DB::table('car_sia_estados_operacion')
                                  ->where('numero_bloque', $numeroBloqueNuevo)
                                  ->exists();

                if (!$existeEstado) {
                    // Un solo registro para el Estado de la Operación
                    DB::table('car_sia_estados_operacion')->insert([
                        'id_car_sia_operaciones' => null,
                        'id_car_sia_estados'     => $idEstadoReal,
                        'numero_bloque'          => $numeroBloqueNuevo,
                        'created_at'             => $ahora,
                        'updated_at'             => $ahora,
                    ]);
                }
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

                // 7. ACTUALIZACIÓN DE ESTADO DEL BLOQUE PADRE A PROCESADO
                CarSiaBloque::where('numero_bloque', $numeroBloqueNuevo)->update(['estado' => 'PROCESADO']);

                // 8. LOG DE AUDITORÍA
                try {
                    CarSiaOperacionLog::create([
                        'numero_bloque'                 => $numeroBloqueNuevo,
                        'id_car_sia_operaciones_lineas' => null,
                        'id_car_sia_origenes_evento'    => $origen->id,
                        'id_car_sia_eventos_auditoria'  => $evento->id,
                        'id_user'                       => Auth::check() ? Auth::id() : null,
                        'ip'                            => $request->ip() ?? '127.0.0.1',
                        'detalles_ejecucion'            => [
                            'accion'              => 'Generacion Automatica Operacion Lote (Validada contra Maestro)',
                            'bloque_origen'       => $bloqueOrigen,
                            'clientes_procesados' => $clientesProcesados,
                            'registros_ignorados' => $registrosIgnorados,
                            'estado_asignado'     => $idEstadoReal,
                            'tipo_asignado'       => $idTipoEvento
                        ]
                    ]);
                } catch (\Exception $exLog) {
                    Log::warning("Fallo log de auditoría: " . $exLog->getMessage());
                }
            });

            if ($clientesProcesados === 0 && $registrosIgnorados === 0) {
                if ($request->ajax()) return response()->json(['error' => 'No hay lotes válidos.'], 400);
                return redirect()->back()->with('error', 'No se encontraron lotes pendientes.');
            }

            // Mensajes para el usuario final
            session()->flash('inyeccion_exitosa', true);
            session()->flash('resumen_clientes', $clientesProcesados);

            if ($registrosIgnorados > 0) {
                session()->flash('warning', "ATENCIÓN: Se omitieron {$registrosIgnorados} registros de facturas porque su NIT/cédula no existe en el sistema.");
            }

            // --- LIMPIEZA DE MEMORIAS (Pantalla A y Pantalla B) ---
            Cache::forget("kpis_ingesta_staging_bloque_{$bloqueOrigen}");
            Cache::forget('sia_bloques_disponibles');
            Cache::forget('sia_anios_disponibles');

            if ($request->ajax()) return response()->json(['success' => true]);

            return redirect()->back();

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('CERTIFICADOS Ingesta - Error crítico: ' . $e->getMessage());

            if (str_contains($e->getMessage(), 'Ninguna de las cédulas del lote existe en el maestro de terceros')) {
                // Rescatar los terceros únicos que causaron el error para previsualizarlos
                $tercerosFaltantes = \App\Models\Certificados\CarSiaApi::where('numero_bloque', $request->bloque_origen)
                    ->where('estado', 'PENDIENTE')
                    ->select('tercero', 'nombre_tercero')
                    ->whereNotNull('tercero')
                    ->where('tercero', '!=', '')
                    ->distinct()
                    ->get()
                    ->toArray();

                return redirect()->back()
                    ->with('error', 'Faltan clientes en la Maestra de Terceros para procesar este lote.')
                    ->with('requiere_crear_terceros', true)
                    ->with('bloque_fallido', $request->bloque_origen)
                    ->with('lista_faltantes', $tercerosFaltantes);
            }

            if ($request->ajax()) return response()->json(['error' => 'Error SQL: ' . $e->getMessage()], 500);
            return redirect()->back()->with('error', 'Error SQL: ' . $e->getMessage());
        }
    }

    /**
     * =========================================================================
     * 4. EXCLUIR REGISTRO INDIVIDUAL
     * =========================================================================
     */
    public function anularRegistro($id)
    {
        try {
            // 1. Buscamos el registro crudo por su ID único
            $registro = CarSiaApi::findOrFail($id);

            // 2. Le cambiamos el estado a anulado
            $registro->anular = 1;
            $registro->save();

            // 3. Borramos el caché de este bloque
            Cache::forget("kpis_ingesta_staging_bloque_{$registro->numero_bloque}");

            return back()->with('success', "La factura #{$registro->id_factura} fue excluida del bloque.");

        } catch (\Exception $e) {
            \Log::error('CERTIFICADOS Ingesta - Error al anular registro: ' . $e->getMessage());
            return back()->with('error', 'Ocurrió un error al intentar excluir el registro.');
        }
    }

    /**
     * =========================================================================
     * 5. ANULAR LOTE COMPLETO
     * =========================================================================
     */
    public function anularLote($numero_bloque)
    {
        try {
            // 1. Actualizamos masivamente todos los registros que pertenezcan a este bloque
            // y que aún estén pendientes (no podemos anular algo que ya se procesó en el ERP)
            CarSiaApi::where('numero_bloque', $numero_bloque)
                     ->where('estado', '!=', 'PROCESADO')
                     ->update(['anular' => 1]);

            // 2. Borramos el caché del bloque
            Cache::forget("kpis_ingesta_staging_bloque_{$numero_bloque}");

            return redirect()->back()->with('success', "El Lote API-" . str_pad($numero_bloque, 4, '0', STR_PAD_LEFT) . " fue anulado por completo.");

        } catch (\Exception $e) {
            \Log::error('CERTIFICADOS Ingesta - Error al anular lote: ' . $e->getMessage());
            return redirect()->back()->with('error', 'No se pudo anular el lote.');
        }
    }

    /**
     * =========================================================================
     * 6. CREAR TERCEROS FALTANTES EN MAESTRA (OPTIMIZADO POR BLOQUE)
     * =========================================================================
    */
    public function crearTercerosFaltantes(Request $request)
    {
        $request->validate([
            'bloque_origen' => 'required|integer'
        ]);

        $bloqueOrigen = $request->bloque_origen;

        try {
            // 1. Obtener únicos de CarSiaApi para este bloque
            $tercerosApi = CarSiaApi::where('numero_bloque', $bloqueOrigen)
                ->where('estado', 'PENDIENTE')
                ->select('tercero', 'nombre_tercero')
                ->whereNotNull('tercero')
                ->where('tercero', '!=', '')
                ->distinct()
                ->get();

            if ($tercerosApi->isEmpty()) {
                return redirect()->back()->with('error', 'No hay terceros válidos en el lote para crear.');
            }

            // 2. Filtrar los que YA existen en MaeTerceros
            $cedulasApi = $tercerosApi->pluck('tercero')->toArray();
            $existentes = DB::table('MaeTerceros')->whereIn('cod_ter', $cedulasApi)->pluck('cod_ter')->toArray();

            $faltantes = $tercerosApi->whereNotIn('tercero', $existentes);

            if ($faltantes->isEmpty()) {
                return redirect()->back()->with('success', 'Todos los terceros del lote ya existen en la maestra.');
            }

            // 3. Preparar matriz de inserción cuidadosa
            $nuevosTerceros = [];
            foreach ($faltantes as $faltante) {
                $documento = trim($faltante->tercero);
                $nombre = trim($faltante->nombre_tercero) ?: 'SIN NOMBRE REGISTRADO';

                $nuevosTerceros[] = [
                    'cod_ter'   => $documento,
                    'id_ter'    => $documento,
                    'nom_ter'   => $nombre,
                    'raz'       => $nombre,
                    'razon_soc' => $nombre,
                    // Valores genéricos seguros para evitar violaciones de NOT NULL (Ajusta según tu BD)
                    'tip_pers'  => '1', // 1: Natural (por defecto)
                    'tipo_ter'  => 'CLIENTE',
                    'tdoc'      => '13' // 13: Cédula de ciudadanía genérica
                ];
            }

            // 4. Inserción masiva ignorando duplicados
        MaeTerceros::insertOrIgnore($nuevosTerceros);

        // Opcional: Contar cuántas filas de facturas se destrabaron con estos clientes
        $facturasAfectadas = CarSiaApi::where('numero_bloque', $bloqueOrigen)
            ->whereIn('tercero', $faltantes->pluck('tercero'))
            ->count();

        return redirect()->back()->with('success', "Se crearon " . count($nuevosTerceros) . " terceros únicos. Esto permitirá procesar {$facturasAfectadas} facturas pendientes. Ya puedes intentar inyectar el bloque nuevamente.");
        } catch (\Exception $e) {
            Log::error('Error creando terceros desde API: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Error al crear la maestra: ' . $e->getMessage());
        }
    }

    /**
     * =========================================================================
     * 7. PERIODO DE INGESTA (CRUD)
     * =========================================================================
     *  - Listar periodos
     *  - Crear un nuevo periodo
     *  - Editar un periodo existente - Pendiente
     *  - Eliminar un periodo - Pendiente
     * =========================================================================
     */
    public function storePeriodo(Request $request)
    {
        $request->validate([
            'anio'   => 'required|integer|min:2020|max:2099',
            'mes'    => 'required|integer|min:1|max:12',
            'nombre' => 'required|string|max:50',
        ]);

        try {
            // Validar que no exista el mismo año y mes
            $existe = CarSiaPeriodo::where('anio', $request->anio)
                                ->where('mes', $request->mes)->exists();

            if ($existe) {
                return redirect()->back()->with('warning', 'El periodo para este Año y Mes ya existe.');
            }

            CarSiaPeriodo::create([
                'anio'    => $request->anio,
                'mes'     => $request->mes,
                'nombre'  => mb_strtoupper($request->nombre, 'UTF-8'),
                'abierto' => $request->has('abierto') ? 1 : 0,
            ]);

            return redirect()->back()->with('success', "Periodo {$request->nombre} creado correctamente.");
        } catch (\Exception $e) {
            Log::error('CERTIFICADOS - Error al crear periodo: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Ocurrió un error al crear el periodo.');
        }
    }
}
