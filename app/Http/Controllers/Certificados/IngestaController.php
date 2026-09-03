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
use PhpOffice\PhpSpreadsheet\IOFactory;

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
     * 2. CARGAR EXCEL (PROCESAMIENTO DIRECTO EN PRIMER PLANO POR BLOQUES)
     * =========================================================================
     */
    public function cargarExcel(Request $request)
    {
        // 1. Validar parámetros de entrada
        $request->validate([
            'archivo_excel' => 'required|mimes:xlsx,xls,csv|max:51200',
            'id_periodo'    => 'required|integer|exists:car_sia_periodos,id'
        ]);

        try {
            $progresoToken = $request->input('progreso_token');
            $totalFilas = $this->obtenerTotalFilasExcel($request->file('archivo_excel'));

            if ($progresoToken) {
                Cache::put('ingesta_progreso_' . $progresoToken, [
                    'estado' => 'iniciando',
                    'procesadas' => 0,
                    'total' => $totalFilas,
                    'porcentaje' => 0,
                ], now()->addHour());
            }

            // 2. Transacción segura para el número de bloque
            $nuevoBloque = DB::transaction(function () use ($request) {

                $ultimoBloque = CarSiaBloque::lockForUpdate()->orderBy('numero_bloque', 'desc')->first();
                $siguienteBloque = $ultimoBloque ? $ultimoBloque->numero_bloque + 1 : 1;

                // Verificación extra con otras tablas
                $maxStaging    = CarSiaApi::max('numero_bloque') ?? 0;
                $maxOperacion  = CarSiaOperacion::max('numero_bloque') ?? 0;
                $siguienteBloque = max($siguienteBloque, (int)$maxStaging + 1, (int)$maxOperacion + 1);

                CarSiaBloque::create([
                    'numero_bloque' => $siguienteBloque,
                    'id_periodo'    => $request->id_periodo,
                    'descripcion'   => 'Lote masivo #' . $siguienteBloque . ' (MODO DIRECTO)',
                    'estado'        => 'PROCESANDO'
                ]);

                return $siguienteBloque;
            });

            // 3. Preparar el servidor para un proceso largo en primer plano
            set_time_limit(0); // Tiempo ilimitado para que no se caiga a la mitad
            ini_set('memory_limit', '2048M'); // 2GB de RAM permitidos

            // 4. Ejecutar la importación (la clase Import hará el trabajo por bloques)
            Excel::import(new IngestaExcelImport($nuevoBloque, $progresoToken, $totalFilas), $request->file('archivo_excel'));

            // 5. Si termina sin errores, actualizamos el estado
            CarSiaBloque::where('numero_bloque', $nuevoBloque)
                ->update(['estado' => 'PROCESADO']);

            if ($progresoToken) {
                Cache::put('ingesta_progreso_' . $progresoToken, [
                    'estado' => 'completado',
                    'procesadas' => $totalFilas,
                    'total' => $totalFilas,
                    'porcentaje' => 100,
                ], now()->addMinutes(10));
            }

            return redirect()->route('certificados.ingesta.index', ['bloque' => $nuevoBloque])
                             ->with('success', "Archivo procesado exitosamente por bloques. Los registros del Lote #{$nuevoBloque} están listos.");

        } catch (\Exception $e) {
            // Reversión visual si estalla a mitad de lectura
            if (isset($nuevoBloque)) {
                CarSiaBloque::where('numero_bloque', $nuevoBloque)->update(['estado' => 'ERROR']);
            }
            if (isset($progresoToken)) {
                Cache::put('ingesta_progreso_' . $progresoToken, [
                    'estado' => 'error',
                    'mensaje' => 'No fue posible procesar el archivo.',
                    'porcentaje' => 0,
                ], now()->addMinutes(10));
            }
            \Illuminate\Support\Facades\Log::error('CERTIFICADOS Ingesta - Error procesando Excel: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Fallo técnico leyendo el Excel: ' . $e->getMessage());
        }
    }

    public function progresoCarga(Request $request)
    {
        $request->validate(['token' => 'required|uuid']);

        $progreso = Cache::get('ingesta_progreso_' . $request->token);

        if (!$progreso) {
            return response()->json(['estado' => 'no_encontrado'], 404);
        }

        return response()->json($progreso);
    }

    private function obtenerTotalFilasExcel($archivo): int
    {
        try {
            $reader = IOFactory::createReaderForFile($archivo->getRealPath());
            $hojas = call_user_func([$reader, 'listWorksheetInfo'], $archivo->getRealPath());

            return max(0, (int) ($hojas[0]['totalRows'] ?? 0) - 1);
        } catch (\Throwable $e) {
            Log::warning('CERTIFICADOS Ingesta - No se pudo calcular el total de filas: ' . $e->getMessage());
            return 0;
        }
    }

    /**
     * =========================================================================
     * 3. MOTOR DE INYECCIÓN (ALTO RENDIMIENTO - OPTIMIZADO PARA 30K+ REGISTROS)
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
            // Aumentamos los límites para procesos masivos
            ini_set('max_execution_time', 600);
            ini_set('memory_limit', '1024M');

            $bloqueOrigen = $request->bloque_origen;
            $progresoToken = $request->input('progreso_token');

            $clientesProcesados = 0;
            $registrosIgnorados = 0;

            $this->guardarProgresoInyeccion($progresoToken, 'iniciando', 0, 0, 'Preparando el lote...');

            DB::transaction(function () use (&$clientesProcesados, &$registrosIgnorados, $request, $bloqueOrigen, $progresoToken) {

                // 1. OBTENER SÓLO CÉDULAS VÁLIDAS CRUZANDO DIRECTAMENTE EN BASE DE DATOS (JOIN IMPLÍCITO)
                // Esto evita traer 30.000 registros a PHP solo para validarlos
                $cedulasValidas = DB::table('car_sia_api')
                    ->join('MaeTerceros', 'car_sia_api.tercero', '=', 'MaeTerceros.cod_ter')
                    ->where('car_sia_api.numero_bloque', $bloqueOrigen)
                    ->where('car_sia_api.estado', 'PENDIENTE')
                    ->where(function ($query) {
                        $query->whereNull('car_sia_api.anular')->orWhere('car_sia_api.anular', '!=', 1);
                    })
                    ->distinct()
                    ->pluck('car_sia_api.tercero')
                    ->toArray();

                $totalValidas = count($cedulasValidas);

                // Si no hay válidas, calculamos cuántos fallaron para mostrar la alerta
                if ($totalValidas === 0) {
                    $hayPendientes = CarSiaApi::where('numero_bloque', $bloqueOrigen)
                        ->where('estado', 'PENDIENTE')
                        ->where(function ($query) {
                            $query->whereNull('anular')->orWhere('anular', '!=', 1);
                        })->exists();

                    if ($hayPendientes) {
                        throw new \Exception('Ninguna de las cédulas del lote existe en el maestro de terceros (MaeTerceros).');
                    }
                    return; // No hay nada que procesar
                }

                $this->guardarProgresoInyeccion($progresoToken, 'procesando', 10, $totalValidas, 'Terceros validados...');

                // 2. CONTAR REGISTROS IGNORADOS (SIN USAR WHERENOTIN CON ARRAYS GIGANTES)
                // Se usa whereNotExists para que el motor de BD haga el cruce internamente súper rápido
                $registrosIgnorados = CarSiaApi::where('numero_bloque', $bloqueOrigen)
                    ->where('estado', 'PENDIENTE')
                    ->where(function ($query) {
                        $query->whereNull('anular')->orWhere('anular', '!=', 1);
                    })
                    ->whereNotExists(function ($query) {
                        $query->select(DB::raw(1))
                              ->from('MaeTerceros')
                              ->whereColumn('MaeTerceros.cod_ter', 'car_sia_api.tercero');
                    })
                    ->count();

                $origen = CarSiaOrigenEvento::firstOrCreate(['nombre' => 'Interfaz Web']);
                $evento = CarSiaEventoAuditoria::firstOrCreate(['nombre' => 'Inyección Masiva ERP']);

                $anioActual = date('Y');

                // 3. OPTIMIZACIÓN CRÍTICA: REEMPLAZO DE whereYear()
                // Al usar rangos (>= y <=), la base de datos SÍ usa los índices de fecha.
                $inicioAnio = "{$anioActual}-01-01 00:00:00";
                $finAnio    = "{$anioActual}-12-31 23:59:59";

                $cantidadActual = CarSiaOperacion::where('created_at', '>=', $inicioAnio)
                                                 ->where('created_at', '<=', $finAnio)
                                                 ->count();

                $idEstadoReal = $request->id_car_sia_estados;
                $idTipoEvento = $request->id_car_sia_tipos;
                $ahora = now()->format('Y-m-d H:i:s');

                // 4. PREPARACIÓN EN MEMORIA DE LA MATRIZ
                $operacionesMatriz = [];
                $validasProcesadas = 0;

                foreach ($cedulasValidas as $cedula) {
                    $cantidadActual++;
                    $consecutivo = str_pad($cantidadActual, 4, '0', STR_PAD_LEFT);

                    $operacionesMatriz[] = [
                        'numero_radicado' => "INI-{$anioActual}-{$consecutivo}",
                        'numero_bloque'   => $bloqueOrigen,
                        'id_tercero'      => $cedula,
                        'created_at'      => $ahora,
                        'updated_at'      => $ahora,
                    ];
                    $clientesProcesados++;
                }

                // 5. INSERCIÓN MASIVA CHUNKEADA
                foreach (array_chunk($operacionesMatriz, 1000) as $bloque) {
                    CarSiaOperacion::insert($bloque);
                    $validasProcesadas += count($bloque);
                    $porcentaje = 10 + (int) floor(($validasProcesadas / $totalValidas) * 70);

                    $this->guardarProgresoInyeccion(
                        $progresoToken, 'procesando', min(80, $porcentaje), $totalValidas, "Creando operaciones: {$validasProcesadas} de {$totalValidas}"
                    );
                }

                $this->guardarProgresoInyeccion($progresoToken, 'procesando', 85, $totalValidas, 'Actualizando el lote...');

                // 6. INSERCIÓN DE PIVOTES
                $existeTipo = DB::table('car_sia_tipos_operacion')->where('numero_bloque', $bloqueOrigen)->exists();
                if (!$existeTipo) {
                    DB::table('car_sia_tipos_operacion')->insert([
                        'id_car_sia_operaciones' => null,
                        'id_car_sia_tipos'       => $idTipoEvento,
                        'numero_bloque'          => $bloqueOrigen,
                        'created_at'             => $ahora,
                        'updated_at'             => $ahora,
                    ]);
                }

                $existeEstado = DB::table('car_sia_estados_operacion')->where('numero_bloque', $bloqueOrigen)->exists();
                if (!$existeEstado) {
                    DB::table('car_sia_estados_operacion')->insert([
                        'id_car_sia_operaciones' => null,
                        'id_car_sia_estados'     => $idEstadoReal,
                        'numero_bloque'          => $bloqueOrigen,
                        'created_at'             => $ahora,
                        'updated_at'             => $ahora,
                    ]);
                }

                // 7. ACTUALIZACIÓN MASIVA EFICIENTE (SIN USAR whereIn Gigante)
                CarSiaApi::where('numero_bloque', $bloqueOrigen)
                    ->where('estado', 'PENDIENTE')
                    ->where(function ($query) {
                        $query->whereNull('anular')->orWhere('anular', '!=', 1);
                    })
                    ->whereExists(function ($query) {
                        $query->select(DB::raw(1))
                              ->from('MaeTerceros')
                              ->whereColumn('MaeTerceros.cod_ter', 'car_sia_api.tercero');
                    })
                    ->update([
                        'estado'     => 'PROCESADO',
                        'updated_at' => $ahora
                    ]);

                // 8. ACTUALIZAR BLOQUE PADRE
                CarSiaBloque::where('numero_bloque', $bloqueOrigen)->update(['estado' => 'PROCESADO']);

                $this->guardarProgresoInyeccion($progresoToken, 'procesando', 95, $totalValidas, 'Registrando auditoría...');

                // 9. LOG DE AUDITORÍA
                try {
                    CarSiaOperacionLog::create([
                        'numero_bloque'                 => $bloqueOrigen,
                        'id_car_sia_operaciones_lineas' => null,
                        'id_car_sia_origenes_evento'    => $origen->id,
                        'id_car_sia_eventos_auditoria'  => $evento->id,
                        'id_user'                       => Auth::check() ? Auth::id() : null,
                        'ip'                            => $request->ip() ?? '127.0.0.1',
                        'detalles_ejecucion'            => [
                            'accion'              => 'Generacion Automatica Operacion Lote',
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
                $this->guardarProgresoInyeccion($progresoToken, 'error', 0, 0, 'No se encontraron registros pendientes.');
                if ($request->ajax()) return response()->json(['error' => 'No hay lotes válidos.'], 400);
                return redirect()->back()->with('error', 'No se encontraron lotes pendientes.');
            }

            session()->flash('inyeccion_exitosa', true);
            session()->flash('resumen_clientes', $clientesProcesados);

            $this->guardarProgresoInyeccion($progresoToken, 'completado', 100, $clientesProcesados, 'Lote procesado correctamente.');

            if ($registrosIgnorados > 0) {
                session()->flash('warning', "ATENCIÓN: Se omitieron {$registrosIgnorados} registros de facturas porque su NIT/cédula no existe en el sistema.");
            }

            Cache::forget("kpis_ingesta_staging_bloque_{$bloqueOrigen}");
            Cache::forget('sia_bloques_disponibles');
            Cache::forget('sia_anios_disponibles');

            if ($request->ajax()) return response()->json(['success' => true]);

            return redirect()->back();

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('CERTIFICADOS Ingesta - Error crítico: ' . $e->getMessage());

            if (str_contains($e->getMessage(), 'Ninguna de las cédulas del lote existe en el maestro de terceros')) {
                // Recuperar faltantes usando query pura (más rápido)
                $tercerosFaltantes = DB::table('car_sia_api')
                    ->select('tercero', 'nombre_tercero')
                    ->where('numero_bloque', $request->bloque_origen)
                    ->where('estado', 'PENDIENTE')
                    ->whereNotNull('tercero')
                    ->where('tercero', '!=', '')
                    ->whereNotExists(function ($query) {
                        $query->select(DB::raw(1))
                              ->from('MaeTerceros')
                              ->whereColumn('MaeTerceros.cod_ter', 'car_sia_api.tercero');
                    })
                    ->distinct()
                    ->get()
                    ->toArray();

                $tercerosFaltantesArray = json_decode(json_encode($tercerosFaltantes), true);

                $this->guardarProgresoInyeccion($progresoToken ?? null, 'error', 0, 0, 'Faltan terceros en la maestra.');

                return redirect()->back()
                    ->with('error', 'Faltan clientes en la Maestra de Terceros para procesar este lote.')
                    ->with('requiere_crear_terceros', true)
                    ->with('bloque_fallido', $request->bloque_origen)
                    ->with('lista_faltantes', $tercerosFaltantesArray);
            }

            $this->guardarProgresoInyeccion($progresoToken ?? null, 'error', 0, 0, 'No fue posible procesar el lote.');

            if ($request->ajax()) return response()->json(['error' => 'Error SQL: ' . $e->getMessage()], 500);
            return redirect()->back()->with('error', 'Error SQL: ' . $e->getMessage());
        }
    }

    private function guardarProgresoInyeccion(?string $token, string $estado, int $porcentaje, int $total, string $mensaje): void
    {
        if (!$token) {
            return;
        }

        Cache::put('ingesta_progreso_' . $token, [
            'estado' => $estado,
            'procesadas' => $porcentaje === 100 ? $total : 0,
            'total' => $total,
            'porcentaje' => $porcentaje,
            'mensaje' => $mensaje,
        ], $estado === 'completado' || $estado === 'error' ? now()->addMinutes(10) : now()->addHour());
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
            // Usamos una transacción para garantizar que ambas tablas se actualicen juntas
            \Illuminate\Support\Facades\DB::transaction(function () use ($numero_bloque) {

                // 1. Actualizamos masivamente todos los registros en Staging (CarSiaApi)
                // que aún estén pendientes (no podemos anular algo que ya se procesó en el ERP)
                CarSiaApi::where('numero_bloque', $numero_bloque)
                         ->where('estado', '!=', 'PROCESADO')
                         ->update(['anular' => 1]);

                // 2. Actualizamos el estado del bloque padre (CarSiaBloque) a ANULADO
                CarSiaBloque::where('numero_bloque', $numero_bloque)
                            ->update(['estado' => 'ANULADO']);

            });

            // 3. Borramos el caché del bloque para que los KPIs se actualicen inmediatamente
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
