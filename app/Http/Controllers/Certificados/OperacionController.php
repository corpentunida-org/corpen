<?php

namespace App\Http\Controllers\Certificados;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;

use App\Models\Certificados\CarSiaOperacion;
use App\Models\Certificados\CarSiaOperacionLinea;
use App\Models\Certificados\CarSiaEstadoOperacion;
use App\Models\Certificados\CarSiaTipoOperacion;
use App\Models\Certificados\CarSiaOperacionAlerta;
use App\Models\Certificados\CarSiaOperacionConfig;

use App\Models\Certificados\CarSiaEstado;
use App\Models\Certificados\CarSiaTipo;
use App\Models\Certificados\CarSiaTipoAlerta;
use App\Models\Certificados\CarSiaConfig;
use App\Models\Certificados\CarSiaApi;
use App\Models\Certificados\CarSiaOperacionLog;
use App\Models\Maestras\MaeTerceros;
use App\Models\User;
use App\Models\Certificados\CarSiaOrigenEvento;
use App\Models\Certificados\CarSiaEventoAuditoria;
use App\Models\Certificados\CarSiaOperacionAlertaLog;
use App\Traits\LogAuditoriaTrait;


class OperacionController extends Controller
{
    use LogAuditoriaTrait;

    /**
     * 1. GESTIÓN MATRIZ: Listar el motor de operaciones aislado por LOTES
     */
    public function index(Request $request)
    {
        try {
            // SOLUCIÓN INTEGRADA: Consulta directa a la tabla de bloques excluyendo los anulados
            $bloquesDisponibles = Cache::remember('sia_bloques_disponibles', 5, function () {
                return DB::table('car_sia_bloques')
                    ->where('estado', '!=', 'ANULADO')
                    ->orderBy('numero_bloque', 'desc')
                    ->get();
            });

            $bloqueActivo = $request->input('bloque', $bloquesDisponibles->first()?->numero_bloque);

            $kpi = ['total' => 0, 'procesados' => 0, 'pendientes' => 0];

            $historialBloque = collect();
            $operacionesConfiguradas = collect();
            $configuracionesMasivas = collect(); // <--- NUEVA COLECCIÓN PARA LAS MASIVAS

            if ($bloqueActivo) {
                $kpi['total'] = CarSiaOperacion::where('numero_bloque', $bloqueActivo)->count();

                $kpi['procesados'] = CarSiaOperacion::where('numero_bloque', $bloqueActivo)
                    ->whereExists(function ($query) {
                        $query->select(DB::raw(1))
                              ->from('car_sia_estados_operacion as eo')
                              ->join('car_sia_estados as e', 'eo.id_car_sia_estados', '=', 'e.id')
                              ->whereColumn('eo.id_car_sia_operaciones', 'car_sia_operaciones.id')
                              ->where(function($q) {
                                  $q->where('e.nombre', 'LIKE', '%Procesado%')
                                    ->orWhere('e.nombre', 'LIKE', '%Aprobado%')
                                    ->orWhere('e.nombre', 'LIKE', '%Completado%');
                              });
                    })->count();

                $kpi['pendientes'] = $kpi['total'] - $kpi['procesados'];

                $historialBloque = CarSiaOperacionLog::with(['usuario', 'eventoAuditoria'])
                    ->where('numero_bloque', $bloqueActivo)
                    ->orderBy('created_at', 'desc')
                    ->take(8)
                    ->get();

                // 1. EXTRAER CONFIGURACIONES INDIVIDUALES
                $operacionesConfiguradas = CarSiaOperacion::with([
                        'tercero',
                        'configuracion.configuracionBase.accionVencimiento'
                    ])
                    ->where('numero_bloque', $bloqueActivo)
                    ->whereHas('configuracion')
                    ->orderBy('created_at', 'desc')
                    ->get();

                // 2. EXTRAER CONFIGURACIONES MASIVAS (El que le faltaba el id_car_sia_operaciones)
                $configuracionesMasivas = CarSiaOperacionConfig::with('configuracionBase.accionVencimiento')
                    ->where('numero_bloque', $bloqueActivo)
                    ->whereNull('id_car_sia_operaciones') // Es masivo, no tiene operacion específica
                    ->orderBy('created_at', 'desc')
                    ->get();

                // 3. EXTRAER ALERTAS DEL BLOQUE (Masivas e Individuales)
                $alertasBloqueActivo = \App\Models\Certificados\CarSiaOperacionAlerta::with(['tipoAlerta', 'operacion', 'usuario'])
                    ->where('numero_bloque', $bloqueActivo)
                    ->orderBy('created_at', 'desc')
                    ->get();

                // 4. EXTRAER TIPOLOGÍAS/TIPOS DEL BLOQUE (Masivas e Individuales)
                $tiposBloqueActivo = \App\Models\Certificados\CarSiaTipoOperacion::with(['tipo', 'operacion', 'usuario'])
                    ->where('numero_bloque', $bloqueActivo)
                    ->orderBy('created_at', 'desc')
                    ->get();
            }

            $query = CarSiaOperacion::with([
                'tercero',
                'lineas.factura',
                'estados.estado', 'estadosBloque.estado',
                'tipos.tipo', 'tiposBloque.tipo',
                'alertas.tipoAlerta', 'alertasBloque.tipoAlerta',
                'configuracion.configuracionBase.accionVencimiento'
            ]);

            if ($bloqueActivo) {
                $query->where('numero_bloque', $bloqueActivo);
            } else {
                $query->where('id', 0);
            }

            if ($request->filled('anio')) {
                $query->whereYear('created_at', $request->anio);
            }

            if ($request->filled('buscar')) {
                $search = trim($request->buscar);
                $query->where(function($q) use ($search) {
                    $q->where('numero_radicado', 'LIKE', "%{$search}%")
                      ->orWhereHas('tercero', function($qTer) use ($search) {
                          $qTer->where('nom_ter', 'like', "%{$search}%")
                               ->orWhere('cod_ter', 'like', "%{$search}%");
                      });
                });
            }

            $operaciones = $query->orderBy('created_at', 'desc')->paginate(5)->withQueryString();

            $aniosDisponibles = Cache::remember('sia_anios_disponibles', 3600, function () {
                return CarSiaOperacion::whereNotNull('created_at')
                    ->selectRaw('YEAR(created_at) as anio')
                    ->groupBy('anio')
                    ->orderBy('anio', 'desc')
                    ->pluck('anio');
            });

            $tiposAlerta = CarSiaTipoAlerta::all();
            $tipos = CarSiaTipo::all();
            $configuracionesBase = CarSiaConfig::with('accionVencimiento')->get();

            return view('certificados.operaciones.index', compact(
                'operaciones',
                'aniosDisponibles',
                'bloquesDisponibles',
                'bloqueActivo',
                'kpi',
                'tiposAlerta',
                'tipos',
                'historialBloque',
                'configuracionesBase',
                'operacionesConfiguradas',
                'configuracionesMasivas',
                'alertasBloqueActivo',
                'tiposBloqueActivo'
            ));

        } catch (\Exception $e) {
            Log::error("SIA - Error Index: " . $e->getMessage());
            abort(500, 'Error al cargar la matriz de operaciones.');
        }
    }

    /**
     * 2. DETALLE CRÉDITOS: Mostrar toda la trazabilidad de una operación
     */
    public function show($id)
    {
        try {
            $operacion = CarSiaOperacion::with([
                'tercero',
                'lineas' => function($query) {
                    $query->orderBy('created_at', 'desc');
                },
                'lineas.lineaSia',
                'lineas.estadoOperacion',
                'lineas.factura'
                // Ya no es estrictamente necesario cargar 'configuracion' aquí
                // porque lo haremos con una consulta más precisa abajo.
            ])->findOrFail($id);

            $lineasUnicas = $operacion->lineas->unique('id_factura');

            $registrosCrudos = CarSiaApi::with('lineaSia')
                ->where('numero_bloque', $operacion->numero_bloque)
                ->where('tercero', $operacion->id_tercero)
                ->get();

            // --- LÓGICA DE BLADE TRASLADADA (Tab 1: Líneas y Facturas) ---
            $lineasAgrupadas = $registrosCrudos->groupBy(function($item) {
                return $item->lineaSia->nombre
                    ?? $item->nombre_cuenta
                    ?? $item->cuenta
                    ?? 'Línea Desconocida';
            })->map(function($facturas) {
                $totalLinea = $facturas->sum('valor');
                $facturasOrdenadas = $facturas->sortBy('cuota')->map(function($factura) {
                    if ($factura->fecha_venci) {
                        $fechaV = \Carbon\Carbon::parse($factura->fecha_venci);
                        $factura->diasMoraCalculados = now()->diffInDays($fechaV, false);
                        $factura->fechaVFormateada = $fechaV->format('d/m/Y');
                    } else {
                        $factura->diasMoraCalculados = 0;
                        $factura->fechaVFormateada = null;
                    }
                    return $factura;
                });
                return [
                    'total' => $totalLinea,
                    'count' => $facturas->count(),
                    'facturas' => $facturasOrdenadas
                ];
            });

            $estados = CarSiaEstado::all();
            $tipos = CarSiaTipo::all();
            $tiposAlerta = CarSiaTipoAlerta::all();

            // --- LÓGICA DE BLADE TRASLADADA (Tab 3: Auditoría y Detalle Formateado) ---
            $logsAuditoria = CarSiaOperacionLog::with(['origenEvento', 'eventoAuditoria', 'usuario', 'usuario.cargoRelation'])
                ->where('numero_bloque', $operacion->numero_bloque)
                ->orderBy('created_at', 'desc')
                ->get()
                ->map(function ($log) use ($tipos, $estados) {
                    // Pre-calculando variables visuales
                    $log->tituloEvento  = optional($log->eventoAuditoria)->nombre ?? 'Evento Registrado';
                    $log->fechaEvento   = $log->created_at->format('d/m/Y h:i A');
                    $log->origenEvento  = optional($log->origenEvento)->nombre ?? 'Sistema';
                    $log->nombreUsuario = optional($log->usuario)->name ?? 'Sistema';
                    $log->cargoUsuario  = optional(optional($log->usuario)->cargoRelation)->nombre_cargo ?? 'Sin cargo';
                    $log->ipDelUsuario  = $log->ip ?? '127.0.0.1';
                    $log->hayDetalles   = !empty($log->detalles_ejecucion);

                    // Formateando el JSON de detalles
                    $detallesProcesados = [];
                    if ($log->hayDetalles) {
                        foreach ((array)$log->detalles_ejecucion as $llave => $valor) {
                            $llaveLimpia = ucfirst(str_replace('_', ' ', $llave));
                            $valorLimpio = is_array($valor) ? json_encode($valor) : $valor;

                            if ($llave === 'tipo_asignado') {
                                $tipoBuscado = collect($tipos)->firstWhere('id', $valor);
                                $valorLimpio = $tipoBuscado ? $tipoBuscado->nombre : $valor;
                            }
                            if ($llave === 'estado_asignado') {
                                $estadoBuscado = collect($estados)->firstWhere('id', $valor);
                                $valorLimpio = $estadoBuscado ? $estadoBuscado->nombre : $valor;
                            }
                            if ($llave === 'bloque_origen' && is_numeric($valor)) {
                                $valorLimpio = 'API-' . str_pad($valor, 4, '0', STR_PAD_LEFT);
                            }
                            $detallesProcesados[$llaveLimpia] = $valorLimpio;
                        }
                    }
                    $log->detalles_procesados = $detallesProcesados;
                    return $log;
                });

            // --- LÓGICA DE BLADE TRASLADADA (Tab 6: Operarios y Estadísticas) ---
            $operariosData = collect();
            $totalProcesos = collect($logsAuditoria)->count();

            if($totalProcesos > 0) {
                $operariosData = collect($logsAuditoria)->filter(function($log) {
                    return $log->usuario != null;
                })->groupBy(function($log) {
                    return $log->nombreUsuario;
                })->map(function($grupo) use ($totalProcesos) {
                    $cantidad = $grupo->count();
                    return [
                        'nombre'     => $grupo->first()->nombreUsuario,
                        'cargo'      => $grupo->first()->cargoUsuario,
                        'cantidad'   => $cantidad,
                        'porcentaje' => round(($cantidad / $totalProcesos) * 100),
                        'ultimo'     => $grupo->first()->created_at->format('d/m/Y h:i A')
                    ];
                })->sortByDesc('cantidad');
            }

            $historialEstados = CarSiaEstadoOperacion::with('estado')
                ->where('id_car_sia_operaciones', $operacion->id)
                ->orWhere(function($q) use ($operacion) {
                    $q->where('numero_bloque', $operacion->numero_bloque)
                      ->whereNull('id_car_sia_operaciones');
                })
                ->orderBy('created_at', 'desc')
                ->get();

            // --- LÓGICA DE BLADE TRASLADADA (Tab 4: Certificados y Hashes) ---
            $historialTipos = CarSiaTipoOperacion::with('tipo')
                ->where('id_car_sia_operaciones', $operacion->id)
                ->orWhere(function($q) use ($operacion) {
                    $q->where('numero_bloque', $operacion->numero_bloque)
                    ->whereNull('id_car_sia_operaciones');
                })
                ->orderBy('created_at', 'desc')
                ->get()
                ->map(function ($registro) use ($operacion) {
                    $registro->es_lote = is_null($registro->id_car_sia_operaciones);
                    $lineaAsociada = collect($operacion->lineas)->where('id_car_sia_tipos', $registro->id_car_sia_tipos)->first();

                    $registro->nombre_user = 'Usuario / Sistema';
                    $registro->cargo_user = '';

                    if ($lineaAsociada && $lineaAsociada->usuario) {
                        $registro->nombre_user = $lineaAsociada->usuario->name;
                        if ($lineaAsociada->usuario->cargoRelation) {
                            $registro->cargo_user = ' / ' . $lineaAsociada->usuario->cargoRelation->nombre_cargo;
                        }
                    }

                    $lineasParaEsteTipo = collect($operacion->lineas)->where('id_car_sia_tipos', $registro->id_car_sia_tipos);

                    $versionesDeEsteTipo = collect($lineasParaEsteTipo)->groupBy('hash_certificado')->map(function($grupo) {
                        return collect($grupo)->first();
                    })->sortByDesc('created_at');

                    $hashActual = collect($versionesDeEsteTipo)->first()->hash_certificado ?? null;
                    $lineasEditor = collect($lineasParaEsteTipo)->where('hash_certificado', $hashActual)->unique('id_factura');

                    $registro->versionesDeEsteTipo = $versionesDeEsteTipo;
                    $registro->hashActual = $hashActual;
                    $registro->lineasEditor = $lineasEditor;
                    $registro->lineasParaEsteTipo = $lineasParaEsteTipo;

                    return $registro;
                });

            $historialAlertas = CarSiaOperacionAlerta::with('tipoAlerta')
                ->where('id_car_sia_operaciones', $operacion->id)
                ->orWhere(function($q) use ($operacion) {
                    $q->where('numero_bloque', $operacion->numero_bloque)
                      ->whereNull('id_car_sia_operaciones');
                })
                ->orderBy('created_at', 'desc')
                ->get();

            // 2. CONSULTAR Y EVALUAR CONFIGURACIONES (NIVEL OPERACIÓN Y NIVEL BLOQUE)
            $configuraciones = CarSiaOperacionConfig::with(['configuracionBase', 'usuario'])
                ->where('id_car_sia_operaciones', $operacion->id)
                ->orWhere(function($query) use ($operacion) {
                    $query->where('numero_bloque', $operacion->numero_bloque)
                          ->whereNull('id_car_sia_operaciones');
                })
                ->get();

            $operacionesConfiguradas = collect();

            if ($configuraciones->isNotEmpty()) {
                // Inyectamos la relación dinámicamente para que la vista la encuentre
                $operacion->setRelation('configuracion', $configuraciones);

                // Agregamos la operación a la colección que iterará el @foreach
                $operacionesConfiguradas->push($operacion);
            }

            // 3. ENVIAR LA VARIABLE $operacionesConfiguradas AL COMPACT
            return view('certificados.operaciones.show', compact(
                'operacion', 'lineasUnicas', 'historialEstados', 'historialTipos', 'historialAlertas', 'estados', 'tipos', 'tiposAlerta', 'lineasAgrupadas', 'logsAuditoria', 'operariosData', 'operacionesConfiguradas'
            ));

        } catch (\Exception $e) {
            Log::error('🚨 ERROR AL ABRIR EL EXPEDIENTE (SHOW): ' . $e->getMessage());
            return back()->with('error', 'No se pudo cargar el detalle de la operación.');
        }
    }

    /**
     * 3. TRANSICIONA ESTADOS
     */
    public function transicionarEstado(Request $request, $id)
    {
        $request->validate([
            'id_car_sia_estados' => 'required|exists:car_sia_estados,id',
            'numero_bloque'      => 'required|string|max:50'
        ]);

        try {
            DB::transaction(function () use ($request, $id) {
                $operacion = CarSiaOperacion::findOrFail($id);

                CarSiaEstadoOperacion::create([
                    'id_car_sia_operaciones' => $operacion->id,
                    'numero_bloque'          => $request->numero_bloque,
                    'id_car_sia_estados'     => $request->id_car_sia_estados,
                    'id_user'                => Auth::id(),
                ]);

                // Registro en Log de Auditoría
                $estado = CarSiaEstado::find($request->id_car_sia_estados);
                $this->registrarLogAuditoria(
                    $operacion->numero_bloque, 1, 1,
                    'Transición de estado individual', 'Operación', 'Cambio de estado manual para la operación.',
                    ['id_operacion' => $operacion->id],
                    [],
                    ['estado_asignado' => $estado ? $estado->nombre : 'Desconocido']
                );
            });

            return redirect()->back()->with('success', 'Estado de la operación actualizado correctamente.');

        } catch (\Exception $e) {
            Log::error("CERTIFICADOS - Error al transicionar estado en operación {$id}: " . $e->getMessage());
            return redirect()->back()->with('error', 'Ocurrió un error al procesar el cambio de estado.');
        }
    }

    /**
     * 4. ASIGNA TIPOS: UNIFICADO PARA INSERCIÓN CON NUEVO HASH
     */
    public function asignarTipo(Request $request, $id)
    {
        $request->validate([
            'id_car_sia_tipos' => 'required|exists:car_sia_tipos,id',
            'numero_bloque'    => 'required|string|max:50'
        ]);

        try {
            DB::transaction(function () use ($request, $id) {
                $operacion = CarSiaOperacion::findOrFail($id);

                CarSiaTipoOperacion::create([
                    'id_car_sia_operaciones' => $operacion->id,
                    'id_car_sia_tipos'       => $request->id_car_sia_tipos,
                    'numero_bloque'          => $request->numero_bloque,
                    'id_user'                => Auth::id(),
                ]);

                // Llamamos al motor de reglas para que INSERTE las líneas nuevas con el nuevo Hash
                $this->procesarLineasOperacion($operacion);

                // Registro en Log de Auditoría
                $tipo = CarSiaTipo::find($request->id_car_sia_tipos);
                $this->registrarLogAuditoria(
                    $operacion->numero_bloque, 1, 4,
                    'Generación de certificado individual y nuevo hash', 'Operación', 'Se generó un nuevo hash de certificado.',
                    ['id_operacion' => $operacion->id],
                    [],
                    ['tipo_asignado' => $tipo ? $tipo->nombre : 'Desconocido']
                );
            });

            return redirect()->back()->with('success', 'Tipo asignado y nuevo certificado generado con éxito (Nuevo Hash de Auditoría).');

        } catch (\Exception $e) {
            Log::error("SIA - Error al asignar tipo en operación {$id}: " . $e->getMessage());
            return redirect()->back()->with('error', 'Ocurrió un error al asignar el tipo de evento.');
        }
    }

    /**
     * 5. PROGRAMA ALERTAS BLOQUE
     */
    public function programarAlertaBloque(Request $request)
    {
        $request->validate([
            'id_car_sia_tipos_alerta' => 'required',
            'numero_bloque'           => 'required',
            'fecha_programada'        => 'required|date'
        ]);

        DB::transaction(function () use ($request) {
            CarSiaOperacionAlerta::create([
                'id'                      => (string) Str::uuid(),
                'id_car_sia_tipos_alerta' => $request->id_car_sia_tipos_alerta,
                'numero_bloque'           => $request->numero_bloque,
                'id_car_sia_operaciones'  => null,
                'fecha_programada'        => $request->fecha_programada,
                'id_user'                 => Auth::id(),
            ]);

            // Registro en Log de Auditoría
            $tipoAlerta = CarSiaTipoAlerta::find($request->id_car_sia_tipos_alerta);
            $this->registrarLogAuditoria(
                $request->numero_bloque, 1, 16,
                'Alerta programada lote completo', 'Bloque', 'Configuración de alerta masiva.',
                [], [],
                [
                    'alerta_asignada'  => $tipoAlerta ? $tipoAlerta->nombre : 'Desconocida',
                    'fecha_programada' => $request->fecha_programada
                ]
            );
        });

        return back()->with('success', 'Alerta general programada para el lote.');
    }

    /**
     * 6. PROGRAMA ALERTAS INDIVIDUAL
     */
    public function programarAlerta(Request $request, $id)
    {
        $request->validate([
            'id_car_sia_tipos_alerta' => 'required|exists:car_sia_tipos_alerta,id',
            'numero_bloque'           => 'required|string|max:50',
            'fecha_programada'        => 'required|date'
        ]);

        try {
            DB::transaction(function () use ($request, $id) {
                $operacion = CarSiaOperacion::findOrFail($id);

                CarSiaOperacionAlerta::create([
                    'id'                      => (string) Str::uuid(),
                    'id_car_sia_tipos_alerta' => $request->id_car_sia_tipos_alerta,
                    'numero_bloque'           => $request->numero_bloque,
                    'id_car_sia_operaciones'  => $operacion->id,
                    'fecha_programada'        => $request->fecha_programada,
                    'procesado_en'            => null,
                    'id_user'                 => Auth::id(),
                ]);

                // Registro en Log de Auditoría
                $tipoAlerta = CarSiaTipoAlerta::find($request->id_car_sia_tipos_alerta);
                $this->registrarLogAuditoria(
                    $operacion->numero_bloque, 1, 17,
                    'Alerta programada individual', 'Operación', 'Configuración de alerta individual.',
                    ['id_operacion' => $operacion->id], [],
                    [
                        'alerta_asignada'  => $tipoAlerta ? $tipoAlerta->nombre : 'Desconocida',
                        'fecha_programada' => $request->fecha_programada
                    ]
                );
            });

            return redirect()->back()->with('success', 'Alerta programada exitosamente.');

        } catch (\Exception $e) {
            Log::error("SIA - Error al programar alerta en operación {$id}: " . $e->getMessage());
            return redirect()->back()->with('error', 'Ocurrió un error al programar la alerta.');
        }
    }

    /**
     * 7. ACTIVA NOTIFICACIONES
     */
    public function toggleNotificacion(Request $request, $id)
    {
        $request->validate([
            'id_car_sia_config'   => 'required|exists:car_sia_config,id',
            'numero_bloque'       => 'required|string|max:50',
            'estado_notificacion' => 'required|boolean'
        ]);

        try {
            DB::transaction(function () use ($request, $id) {
                $operacion = CarSiaOperacion::findOrFail($id);

                CarSiaOperacionConfig::updateOrCreate(
                    [
                        'id_car_sia_operaciones' => $operacion->id,
                        'id_car_sia_config'      => $request->id_car_sia_config,
                        'numero_bloque'          => $request->numero_bloque,
                    ],
                    [
                        'estado_notificacion' => $request->estado_notificacion,
                    ]
                );

                $this->registrarLogAuditoria(
                    $operacion->numero_bloque, 1, 7,
                    'Cambio de Notificación', 'Operación', 'Se ' . ($request->estado_notificacion ? 'activó' : 'desactivó') . ' la alerta de notificación.',
                    ['id_operacion' => $operacion->id],
                    [],
                    ['estado_asignado' => $request->estado_notificacion ? 'Activada' : 'Desactivada']
                );
            });

            $estado = $request->estado_notificacion ? 'activada' : 'desactivada';
            return redirect()->back()->with('success', "Notificación {$estado} correctamente.");

        } catch (\Exception $e) {
            Log::error("SIA - Error al configurar notificaciones en operación {$id}: " . $e->getMessage());
            return redirect()->back()->with('error', 'Ocurrió un error al modificar las configuraciones de notificación.');
        }
    }

    // =========================================================================
    // PROCESAMIENTO MASIVO, INDIVIDUAL Y MOTOR DE HASH
    // =========================================================================

    /**
     * 8. HELPER: Obtener Tipo Dinámico y Generar Hash (UNIFICACIÓN)
     */
    private function obtenerDatosAuditoria($operacionId, $numeroBloque, $timestamp = null)
    {
        $timestamp = $timestamp ?? now()->timestamp;

        // Prioridad 1: Tipo específico de la operación
        $tipoOperacion = CarSiaTipoOperacion::where('id_car_sia_operaciones', $operacionId)
            ->latest()
            ->first();

        // Prioridad 2: Tipo global del bloque (si no tiene individual)
        if (!$tipoOperacion) {
            $tipoOperacion = CarSiaTipoOperacion::where('numero_bloque', $numeroBloque)
                ->whereNull('id_car_sia_operaciones')
                ->latest()
                ->first();
        }

        $id_tipo = $tipoOperacion ? $tipoOperacion->id_car_sia_tipos : 'N/A';
        $hash = "API-{$numeroBloque}-TIPO-{$id_tipo}-OP-{$operacionId}-TS-{$timestamp}";

        return [
            'id_tipo' => $id_tipo !== 'N/A' ? $id_tipo : null,
            'hash'    => $hash,
            'user_id' => Auth::id()
        ];
    }

    /**
     * 9. GENERACIÓN MASIVA (Soporta múltiples tipos dentro del mismo lote)
     */
    public function generarMasivo(Request $request)
    {
        $bloque = $request->input('bloque') ?? $request->input('numero_bloque');
        $id_car_sia_tipos = $request->input('id_car_sia_tipos');

        if (!$bloque) {
            return back()->with('error', 'Debe seleccionar un lote (bloque) válido para procesar.');
        }

        if (!$id_car_sia_tipos) {
            return back()->with('error', 'Debe seleccionar un tipo de certificado.');
        }

        try {
            DB::beginTransaction();

            $ahora = now();
            $timestamp = $ahora->timestamp;
            $id_user = Auth::id();
            $totalOperacionesProcesadas = 0;

            // 1. Registrar el evento de TIPO a nivel Lote (id_car_sia_operaciones = null)
            CarSiaTipoOperacion::create([
                'id_car_sia_operaciones' => null,
                'numero_bloque'          => $bloque,
                'id_car_sia_tipos'       => $id_car_sia_tipos,
                'id_user'                => $id_user,
            ]);

            $id_tipo_global = $id_car_sia_tipos;

            CarSiaOperacion::where('numero_bloque', $bloque)
                ->chunkById(500, function ($operacionesChunk) use ($ahora, $timestamp, $id_user, $id_tipo_global, $bloque, &$totalOperacionesProcesadas) {

                    $tercerosIds = $operacionesChunk->pluck('id_tercero')->toArray();
                    $operacionesIds = $operacionesChunk->pluck('id')->toArray();
                    $operacionesMap = $operacionesChunk->keyBy('id_tercero');

                    // Pre-cargamos los tipos individuales de este chunk para no consultar 1 a 1 en el bucle
                    $tiposIndividuales = CarSiaTipoOperacion::whereIn('id_car_sia_operaciones', $operacionesIds)
                        ->orderBy('created_at', 'desc')
                        ->get()
                        ->groupBy('id_car_sia_operaciones');

                    $facturasChunk = CarSiaApi::where('numero_bloque', $bloque)
                        ->whereIn('tercero', $tercerosIds)
                        ->get();

                    $lineasAInsertar = [];

                    foreach ($facturasChunk as $factura) {
                        $operacion = $operacionesMap[$factura->tercero] ?? null;

                        if (!$operacion) continue;

                        $diasMora = 0;
                        if ($factura->fecha_venci) {
                            $fechaVencimiento = Carbon::parse($factura->fecha_venci);
                            $diferencia = $ahora->diffInDays($fechaVencimiento, false);
                            $diasMora = $diferencia < 0 ? abs((int)$diferencia) : 0;
                        }

                        $calificacion = match(true) {
                            $diasMora > 60 => 'Irregular',
                            $diasMora > 30 => 'Regular',
                            default => 'Bueno'
                        };

                        $id_tipo_final = isset($tiposIndividuales[$operacion->id])
                            ? $tiposIndividuales[$operacion->id]->first()->id_car_sia_tipos
                            : $id_tipo_global;

                        $hash_certificado = "API-{$bloque}-TIPO-{$id_tipo_final}-OP-{$operacion->id}-TS-{$timestamp}";

                        $lineasAInsertar[] = [
                            'id_car_sia_operaciones' => $operacion->id,
                            'id_factura'             => $factura->id,
                            'id_car_sia_lineas'      => $factura->cuenta,
                            'numero_bloque'          => $bloque,
                            'observacion'            => "El asociado presenta una calificación $calificacion debido a un registro de $diasMora días de mora.",
                            'calificacion'           => $calificacion,
                            'fecha_venci'            => $factura->fecha_venci,
                            'id_car_sia_estados'     => 3,
                            'dias_mora_automaticos'  => $diasMora,
                            'procesado_en'           => $ahora->format('Y-m-d H:i:s'),
                            'id_user'                => $id_user,
                            'id_car_sia_tipos'       => $id_tipo_final !== 'N/A' ? $id_tipo_final : null,
                            'hash_certificado'       => $hash_certificado,
                        ];
                    }

                    if (!empty($lineasAInsertar)) {
                        collect($lineasAInsertar)->chunk(1000)->each(function ($batch) {
                            CarSiaOperacionLinea::upsert(
                                $batch->toArray(),
                                // Agregar hash_certificado a las llaves únicas para que inserte versiones nuevas
                                ['id_car_sia_operaciones', 'id_factura', 'hash_certificado'],
                                [
                                    'id_car_sia_lineas', 'numero_bloque', 'observacion', 'calificacion',
                                    'fecha_venci', 'id_car_sia_estados', 'dias_mora_automaticos', 'procesado_en',
                                    'id_user', 'id_car_sia_tipos'
                                ]
                            );
                        });
                    }

                    $totalOperacionesProcesadas += $operacionesChunk->count();
                });

            // Registro en Log de Auditoría Masiva
            $tipo = CarSiaTipo::find($id_car_sia_tipos);
            $this->registrarLogAuditoria(
                $bloque, 1, 3,
                'Generación masiva de certificados', 'Bloque', 'Creación en lote de nuevos hashes y asignación de tipología.',
                [],
                ['registros_afectados' => $totalOperacionesProcesadas],
                ['tipo_asignado' => $tipo ? $tipo->nombre : 'Desconocido']
            );

            DB::commit();

            return back()->with('success', "Procesamiento masivo completado: Lote $bloque procesado exitosamente y asignado al tipo seleccionado ($totalOperacionesProcesadas operaciones).");

        } catch (\Exception $e) {
            DB::rollBack();
            \Illuminate\Support\Facades\Log::error("Error en procesamiento masivo: " . $e->getMessage() . " en la línea " . $e->getLine());
            return back()->with('error', 'Ocurrió un error en la base de datos: ' . $e->getMessage());
        }
    }

    /**
     * 10. VISOR INDIVIDUAL (Solo lectura para el iframe)
     */
    public function generarIndividual(Request $request, $id)
    {
        try {
            $operacion = CarSiaOperacion::with('tercero')->findOrFail($id);

            // 1. Obtener el hash solicitado por la URL, o en su defecto, el último generado
            $hashFiltro = $request->query('hash');

            if (!$hashFiltro) {
                $hashFiltro = CarSiaOperacionLinea::where('id_car_sia_operaciones', $operacion->id)
                    ->where('numero_bloque', $operacion->numero_bloque)
                    ->orderBy('created_at', 'desc')
                    ->value('hash_certificado');
            }

            // 2. Buscar las líneas estrictamente asociadas a ese hash
            $lineas = CarSiaOperacionLinea::where('id_car_sia_operaciones', $operacion->id)
                ->where('numero_bloque', $operacion->numero_bloque)
                ->where('hash_certificado', $hashFiltro)
                ->get();

            if ($lineas->isEmpty()) {
                // Si no hay líneas, puedes retornar un PDF en blanco o un mensaje de error
                abort(404, 'No hay datos procesados para generar este certificado.');
            }

            // 3. Generar el PDF SIN hacer registros en BD ni llamar a procesarLineasOperacion
            $pdf = Pdf::loadView('certificados.pdf.certificado_aldia', compact('operacion', 'lineas'));
            return $pdf->stream("Certificado_{$operacion->numero_radicado}.pdf");

        } catch (\Exception $e) {
            Log::error("Error al renderizar certificado: " . $e->getMessage());
            return back()->with('error', 'Ocurrió un error al mostrar el certificado.');
        }
    }


    // =========================================================================
    // INICIO PROCESAMIENTO INDIVIDUAL Y ACTUALIZACION
    // =========================================================================

        /**
         * 11. MOTOR DE REGLAS INTERNO (Para procesamiento 1 a 1) - SHOW
         */
        private function procesarLineasOperacion($operacion)
        {
            $facturas = CarSiaApi::where('numero_bloque', $operacion->numero_bloque)
                ->where('tercero', $operacion->id_tercero)
                ->get();

            $auditoria = $this->obtenerDatosAuditoria($operacion->id, $operacion->numero_bloque);

            foreach ($facturas as $factura) {
                $diasMora = 0;

                if ($factura->fecha_venci) {
                    $fechaVencimiento = Carbon::parse($factura->fecha_venci);
                    $diferencia = now()->diffInDays($fechaVencimiento, false);
                    $diasMora = $diferencia < 0 ? abs((int)$diferencia) : 0;
                }

                $calificacion = match(true) {
                    $diasMora > 60 => 'Irregular',
                    $diasMora > 30 => 'Regular',
                    default => 'Bueno'
                };

                $observacion = "El asociado presenta una calificación $calificacion debido a un registro de $diasMora días de mora.";

                CarSiaOperacionLinea::updateOrCreate(
                    [
                        'id_car_sia_operaciones' => $operacion->id,
                        'numero_bloque'          => $operacion->numero_bloque,
                        'id_factura'             => $factura->id,
                        'hash_certificado'       => $auditoria['hash'],
                    ],
                    [
                        'id_car_sia_lineas'      => $factura->cuenta,
                        'observacion'            => $observacion,
                        'calificacion'           => $calificacion,
                        'fecha_venci'            => $factura->fecha_venci,
                        'id_car_sia_estados'     => 3,
                        'dias_mora_automaticos'  => $diasMora,
                        'procesado_en'           => now(),
                        'id_user'                => $auditoria['user_id'],
                        'id_car_sia_tipos'       => $auditoria['id_tipo'],
                    ]
                );
            }
        }

        /**
         * 12. ACTUALIZAR LÍNEAS DESDE VISTA HOJA DE CÁLCULO - SHOW
         */
        public function actualizarLineas(Request $request, $id)
        {
            $request->validate([
                'lineas'                               => 'required|array',
                'lineas.*.calificacion'                => 'required|string',
                // Se asegura de que el estado exista realmente en la maestra
                'lineas.*.id_car_sia_estados'          => 'nullable|exists:car_sia_estados,id',
                'lineas.*.dias_mora_automaticos'       => 'required|numeric',
                'lineas.*.fecha_venci'                 => 'nullable|date',
                'lineas.*.fecha_ultimo_recordatorio'   => 'nullable|date',
                'lineas.*.procesado_en'                => 'nullable|date',
                'lineas.*.observacion'                 => 'nullable|string',
                // Validar contra la tabla de tipos si es posible
                'tipo_certificado_id'                  => 'nullable|exists:car_sia_tipos,id'
            ]);

            try {
                DB::transaction(function () use ($request, $id) {
                    $operacion = CarSiaOperacion::findOrFail($id);
                    $auditoria = $this->obtenerDatosAuditoria($operacion->id, $operacion->numero_bloque);

                    // OPTIMIZACIÓN: Cargar todas las líneas a modificar en una sola consulta
                    $lineasIds = array_keys($request->lineas);
                    $lineasOriginales = CarSiaOperacionLinea::whereIn('id', $lineasIds)
                        ->where('id_car_sia_operaciones', $operacion->id)
                        ->where('numero_bloque', $operacion->numero_bloque)
                        ->get()
                        ->keyBy('id'); // Indexar la colección por su ID para acceso rápido

                    // Validar que se encontraron todas las líneas solicitadas
                    if ($lineasOriginales->count() !== count($lineasIds)) {
                        throw new \Exception("Una o más líneas no pertenecen a la operación actual o no existen.");
                    }

                    foreach ($request->lineas as $lineaId => $data) {
                        // Se obtiene de la colección en memoria, no de la BD
                        $lineaOriginal = $lineasOriginales[$lineaId];

                        $nuevaLinea = $lineaOriginal->replicate();

                        $nuevaLinea->calificacion              = $data['calificacion'];
                        $nuevaLinea->id_car_sia_estados        = $data['id_car_sia_estados'] ?? $lineaOriginal->id_car_sia_estados;
                        $nuevaLinea->dias_mora_automaticos     = $data['dias_mora_automaticos'];
                        $nuevaLinea->fecha_venci               = $data['fecha_venci'];
                        $nuevaLinea->fecha_ultimo_recordatorio = $data['fecha_ultimo_recordatorio'] ?? $lineaOriginal->fecha_ultimo_recordatorio;
                        $nuevaLinea->procesado_en              = $data['procesado_en'] ?? $lineaOriginal->procesado_en;
                        $nuevaLinea->observacion               = $data['observacion'] ?? '';
                        $nuevaLinea->numero_bloque             = $operacion->numero_bloque;

                        if ($request->filled('tipo_certificado_id')) {
                            $nuevaLinea->id_car_sia_tipos = $request->tipo_certificado_id;
                        }

                        $nuevaLinea->id_user = $auditoria['user_id'];
                        $nuevaLinea->hash_certificado = $auditoria['hash'];

                        $nuevaLinea->save();

                        // NOTA: Si necesitas desactivar la línea original para evitar sumas duplicadas,
                        // deberías hacerlo aquí, por ejemplo:
                        // $lineaOriginal->update(['es_version_activa' => false]);
                    }

                    // El registro de auditoría masivo permanece igual
                    $this->registrarLogAuditoria(
                        $operacion->numero_bloque, 1, 18,
                        'Modificación manual en hoja de cálculo', 'Operacion', 'Edición manual de parámetros y generación de nueva revisión.',
                        ['id_operacion' => $operacion->id],
                        ['registros_afectados' => count($request->lineas)],
                        ['hash_generado' => $auditoria['hash']]
                    );
                });

                return redirect()->back()->with('success', 'Cambios guardados. Se ha generado una nueva revisión.');

            } catch (\Exception $e) {
                \Illuminate\Support\Facades\Log::error("Error actualizando líneas: " . $e->getMessage());
                return redirect()->back()->with('error', 'Ocurrió un error al intentar guardar los cambios.');
            }
        }

    // =========================================================================
    // FIN PROCESAMIENTO MASIVO, INDIVIDUAL Y SELECTIVO
    // =========================================================================



    public function generarInformeCliente($id)
    {
        // 1. Obtenemos la operación con sus relaciones (tercero y líneas)
        $operacion = CarSiaOperacion::with(['tercero', 'lineas'])->findOrFail($id);

        // 2. Cargamos la vista exacta usando la ruta que indicaste
        $pdf = Pdf::loadView('certificados.operaciones.pdf', compact('operacion'));

        // 3. Mostramos el PDF en el navegador (stream) o forzamos descarga (download)
        return $pdf->stream('informe_cliente_' . $operacion->numero_radicado . '.pdf');
    }

    /* ************************************************************************* */
    // =========================================================================
    // INICIO PROCESAMIENTO MASIVO, INDIVIDUAL Y SELECTIVO
    // =========================================================================

        /**
         * 1. CONFIGURACIÓN MASIVA - INDEX
         * Aplica MÚLTIPLES reglas a TODO el lote.
         */
        public function configuracionMasiva(Request $request)
        {
            $request->validate([
                'numero_bloque'       => 'required|integer',
                'id_car_sia_config'   => 'required|array|min:1',
                'id_car_sia_config.*' => 'exists:car_sia_config,id',
                'justificacion'       => 'nullable|string|max:1000',
                'vigente_hasta'       => 'nullable|date'
            ]);

            $estado = $request->has('estado_notificacion') ? 1 : 0;
            $idUser = Auth::id(); // Capturamos el usuario actual
            $registros = [];
            $ahora = now();

            foreach ($request->id_car_sia_config as $idConfig) {
                $registros[] = [
                    'numero_bloque'          => $request->numero_bloque,
                    'id_car_sia_operaciones' => null,
                    'id_car_sia_config'      => $idConfig,
                    'estado_notificacion'    => $estado,
                    'id_user'                => $idUser,                      // NUEVO
                    'justificacion'          => $request->justificacion,      // NUEVO
                    'vigente_hasta'          => $request->vigente_hasta,      // NUEVO
                    'estado_activo'          => 1,                            // NUEVO (Default Activo)
                    'created_at'             => $ahora,
                    'updated_at'             => $ahora
                ];
            }

            try {
                // Inserción masiva optimizada
                CarSiaOperacionConfig::insert($registros);

                // LOG
                $this->registrarLogAuditoria(
                    $request->numero_bloque, 1, 14, // 14 asumiendo que es ID de Configuraciones
                    'Configuración Masiva de Lote', 'Configuración', 'Se asignaron reglas a todo el bloque.',
                    [],
                    ['registros_afectados' => count($registros)],
                    [],
                    ['observaciones' => $request->justificacion]
                );

                return back()->with('success', count($registros) . " regla(s) general(es) agregada(s) exitosamente al lote API-" . str_pad($request->numero_bloque, 4, '0', STR_PAD_LEFT) . ".");
            } catch (\Exception $e) {
                return back()->with('error', "Error al guardar en BD: " . $e->getMessage());
            }
        }

        /**
         * 2. CONFIGURACIÓN SELECTIVA - INDEX
         * Aplica MÚLTIPLES reglas SOLO a los resultados del buscador.
         */
        public function configuracionSelectiva(Request $request)
        {
            $request->validate([
                'numero_bloque'       => 'required|integer',
                'id_car_sia_config'   => 'required|array|min:1',
                'id_car_sia_config.*' => 'exists:car_sia_config,id',
                'justificacion'       => 'nullable|string|max:1000',
                'vigente_hasta'       => 'nullable|date'
            ]);

            $estado = $request->has('estado_notificacion') ? 1 : 0;
            $idUser = Auth::id(); // Capturamos el usuario actual
            $query = CarSiaOperacion::where('numero_bloque', $request->numero_bloque);

            if ($request->filled('buscar')) {
                $buscar = $request->buscar;
                $query->where(function($q) use ($buscar) {
                    $q->where('numero_radicado', 'like', "%{$buscar}%")
                    ->orWhereHas('tercero', function($qTer) use ($buscar) {
                        $qTer->where('nom_ter', 'like', "%{$buscar}%")
                            ->orWhere('cod_ter', 'like', "%{$buscar}%");
                    });
                });
            }

            if (!$query->exists()) {
                return back()->with('error', "No hay operaciones que coincidan con la búsqueda actual.");
            }

            DB::beginTransaction();
            try {
                $contadorOps = 0;
                $totalReglasAsignadas = 0;
                $ahora = now();

                $query->select('id')->chunk(500, function ($operaciones) use ($request, $estado, $ahora, $idUser, &$contadorOps, &$totalReglasAsignadas) {
                    $registrosBatch = [];

                    foreach ($operaciones as $op) {
                        $contadorOps++;
                        foreach ($request->id_car_sia_config as $idConfig) {
                            $registrosBatch[] = [
                                'numero_bloque'          => $request->numero_bloque,
                                'id_car_sia_operaciones' => $op->id,
                                'id_car_sia_config'      => $idConfig,
                                'estado_notificacion'    => $estado,
                                'id_user'                => $idUser,                 // NUEVO
                                'justificacion'          => $request->justificacion, // NUEVO
                                'vigente_hasta'          => $request->vigente_hasta, // NUEVO
                                'estado_activo'          => 1,                       // NUEVO
                                'created_at'             => $ahora,
                                'updated_at'             => $ahora
                            ];
                            $totalReglasAsignadas++;
                        }
                    }

                    // Inserta el bloque de 500 operaciones
                    CarSiaOperacionConfig::insert($registrosBatch);
                });

                $this->registrarLogAuditoria(
                    $request->numero_bloque, 1, 15,
                    'Configuración Selectiva (Filtro)', 'Operaciones Múltiples', 'Asignación de reglas a un grupo de operaciones filtradas en el buscador.',
                    [],
                    ['registros_afectados' => $contadorOps, 'registros_procesados' => $totalReglasAsignadas],
                    [],
                    ['observaciones' => $request->justificacion]
                );

                DB::commit();
                return back()->with('success', "Se asignaron {$totalReglasAsignadas} reglas selectivas distribuidas en {$contadorOps} operaciones filtradas.");

            } catch (\Exception $e) {
                DB::rollBack();
                return back()->with('error', "Error al procesar la configuración selectiva: " . $e->getMessage());
            }
        }

        /**
         * 3. CONFIGURACIÓN INDIVIDUAL - SHOW
         * Aplica MÚLTIPLES reglas como EXCEPCIÓN a un solo radicado.
         */
        public function configuracionIndividual(Request $request)
        {
            $request->validate([
                'id_operacion'        => 'required|exists:car_sia_operaciones,id',
                'id_car_sia_config'   => 'required|array|min:1',
                'id_car_sia_config.*' => 'exists:car_sia_config,id',
                'justificacion'       => 'nullable|string|max:1000',
                'vigente_hasta'       => 'nullable|date'
            ]);

            $estado = $request->has('estado_notificacion') ? 1 : 0;
            $idUser = Auth::id(); // Capturamos el usuario actual
            $operacion = CarSiaOperacion::findOrFail($request->id_operacion);

            $registros = [];
            $ahora = now();

            foreach ($request->id_car_sia_config as $idConfig) {
                $registros[] = [
                    'numero_bloque'          => $operacion->numero_bloque,
                    'id_car_sia_operaciones' => $operacion->id,
                    'id_car_sia_config'      => $idConfig,
                    'estado_notificacion'    => $estado,
                    'id_user'                => $idUser,
                    'justificacion'          => $request->justificacion,
                    'vigente_hasta'          => $request->vigente_hasta,
                    'estado_activo'          => 1,
                    'created_at'             => $ahora,
                    'updated_at'             => $ahora
                ];
            }

            try {
                CarSiaOperacionConfig::insert($registros);

                $this->registrarLogAuditoria(
                    $operacion->numero_bloque, 1, 15,
                    'Configuración de Excepción', 'Operación', 'Se agregó una regla de excepción a un radicado específico.',
                    ['id_operacion' => $operacion->id, 'numero_radicado' => $operacion->numero_radicado],
                    ['registros_afectados' => count($registros)],
                    [],
                    ['observaciones' => $request->justificacion]
                );

                return back()->with('success', count($registros) . " regla(s) de excepción agregada(s) para el radicado {$operacion->numero_radicado}.");
            } catch (\Exception $e) {
                return back()->with('error', "Error al guardar en BD: " . $e->getMessage());
            }
        }

        public function toggleEstado($id)
        {
            $registro = CarSiaOperacionConfig::findOrFail($id);
            $registro->estado_activo = !$registro->estado_activo; // Invierte el estado
            $registro->save();

            $this->registrarLogAuditoria(
                $registro->numero_bloque, 1, 7,
                'Cambio de Estado de Regla', 'Configuración', 'Se ' . ($registro->estado_activo ? 'activó' : 'inactivó') . ' una regla de configuración.',
                ['id_operacion' => $registro->id_car_sia_operaciones],
                [],
                ['estado_asignado' => $registro->estado_activo ? 'Activa' : 'Inactiva']
            );

            return back()->with('success', 'Estado actualizado correctamente.');
        }

    // =========================================================================
    // FIN PROCESAMIENTO MASIVO, INDIVIDUAL Y SELECTIVO
    // =========================================================================

    /**
     * ACTUALIZAR DATOS MAESTROS DEL TERCERO DESDE LA OPERACIÓN
     */
    public function actualizarTercero(Request $request, $id)
    {
        $request->validate([
            'nom1'  => 'required|string|max:50',
            'nom2'  => 'nullable|string|max:50',
            'apl1'  => 'required|string|max:50',
            'apl2'  => 'nullable|string|max:50',
            'tel'   => 'nullable|string|max:20',
            'tel1'  => 'nullable|string|max:20',
            'dir'   => 'nullable|string|max:150',
            'email' => 'nullable|email|max:100',
        ]);

        try {
            $operacion = CarSiaOperacion::with('tercero')->findOrFail($id);
            $tercero = $operacion->tercero;

            if (!$tercero) {
                return redirect()->back()->with('error', 'No se encontró el tercero en las maestras.');
            }

            // Concatenar el nombre completo y limpiar espacios dobles por si hay campos vacíos
            $nombreCompleto = "{$request->nom1} {$request->nom2} {$request->apl1} {$request->apl2}";
            $nombreConcatenado = trim(preg_replace('/\s+/', ' ', $nombreCompleto));

            $tercero->update([
                'nom1'    => trim($request->nom1),
                'nom2'    => trim($request->nom2),
                'apl1'    => trim($request->apl1),
                'apl2'    => trim($request->apl2),
                'nom_ter' => $nombreConcatenado,
                'tel'     => trim($request->tel),
                'tel1'    => trim($request->tel1),
                'dir'     => trim($request->dir),
                'email'   => trim($request->email),
            ]);

            // (Opcional) Registrar en la auditoría si manejas trazabilidad de maestras
            // $this->registrarLogAuditoria( ... );

            return redirect()->back()->with('success', 'Datos del cliente actualizados y concatenados correctamente.');

        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error("Error actualizando tercero desde operaciones: " . $e->getMessage());
            return redirect()->back()->with('error', 'Ocurrió un error al actualizar los datos del cliente.');
        }
    }

}
