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
use App\Models\Maestras\MaeDistritos;
use App\Models\Maestras\MaeTipo;
use App\Models\Maestras\MaeCongregacion;

class OperacionController extends Controller
{
    use LogAuditoriaTrait;

    /**
     * 1. GESTIÓN MATRIZ: Listar el motor de operaciones aislado por LOTES
     */
    public function index(Request $request)
    {
        try {
            // Caché de bloques excluyendo los anulados
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
            $configuracionesMasivas = collect();
            $alertasBloqueActivo = collect();
            $tiposBloqueActivo = collect();

            if ($bloqueActivo) {
                // OPTIMIZACIÓN KPI 1: Conteo total directo
                $kpi['total'] = CarSiaOperacion::where('numero_bloque', $bloqueActivo)->count();

                // OPTIMIZACIÓN KPI 2: Query Builder directo con Distinct (Evita escaneo repetitivo de Eloquent)
                $kpi['procesados'] = DB::table('car_sia_operaciones as op')
                    ->join('car_sia_estados_operacion as eo', 'op.id', '=', 'eo.id_car_sia_operaciones')
                    ->join('car_sia_estados as e', 'eo.id_car_sia_estados', '=', 'e.id')
                    ->where('op.numero_bloque', $bloqueActivo)
                    ->where(function($q) {
                        $q->where('e.nombre', 'LIKE', '%Procesado%')
                          ->orWhere('e.nombre', 'LIKE', '%Aprobado%')
                          ->orWhere('e.nombre', 'LIKE', '%Completado%');
                    })
                    ->distinct('op.id')
                    ->count('op.id');

                $kpi['pendientes'] = $kpi['total'] - $kpi['procesados'];

                // Historial del Lote
                $historialBloque = CarSiaOperacionLog::with(['usuario', 'eventoAuditoria'])
                    ->where('numero_bloque', $bloqueActivo)
                    ->orderBy('created_at', 'desc')
                    ->take(8)
                    ->get();

                // Paginaciones de las tablas superiores (Lotes, Excepciones, Alertas, Tipos)
                $operacionesConfiguradas = CarSiaOperacion::with(['tercero', 'configuracion.configuracionBase.accionVencimiento'])
                    ->where('numero_bloque', $bloqueActivo)
                    ->whereHas('configuracion')
                    ->orderBy('created_at', 'desc')
                    ->paginate(5, ['*'], 'page_configs')->withQueryString();

                $configuracionesMasivas = CarSiaOperacionConfig::with('configuracionBase.accionVencimiento')
                    ->where('numero_bloque', $bloqueActivo)
                    ->whereNull('id_car_sia_operaciones')
                    ->orderBy('created_at', 'desc')
                    ->paginate(5, ['*'], 'page_masivas')->withQueryString();

                $alertasBloqueActivo = \App\Models\Certificados\CarSiaOperacionAlerta::with(['tipoAlerta', 'operacion', 'usuario'])
                    ->where('numero_bloque', $bloqueActivo)
                    ->orderBy('created_at', 'desc')
                    ->paginate(5, ['*'], 'page_alertas')->withQueryString();

                $tiposBloqueActivo = \App\Models\Certificados\CarSiaTipoOperacion::with(['tipo', 'operacion', 'usuario'])
                    ->where('numero_bloque', $bloqueActivo)
                    ->orderBy('created_at', 'desc')
                    ->paginate(5, ['*'], 'page_tipos')->withQueryString();
            }

            // OPTIMIZACIÓN PRINCIPAL: Se removieron 'lineas.factura' y 'configuracion...' para evitar colapso de memoria
            $query = CarSiaOperacion::with([
                'tercero',
                'estados.estado', 'estadosBloque.estado',
                'tipos.tipo', 'tiposBloque.tipo',
                'alertas.tipoAlerta', 'alertasBloque.tipoAlerta'
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

            // Paginación rápida de la tabla inferior
            $operaciones = $query->orderBy('created_at', 'desc')->paginate(5)->withQueryString();

            $aniosDisponibles = Cache::remember('sia_anios_disponibles', 3600, function () {
                return DB::table('car_sia_operaciones')
                    ->whereNotNull('created_at')
                    ->selectRaw('YEAR(created_at) as anio')
                    ->groupBy('anio')
                    ->orderBy('anio', 'desc')
                    ->pluck('anio');
            });

            // Catálogos para modales
            $tiposAlerta = CarSiaTipoAlerta::all();
            $tipos = CarSiaTipo::all();
            $configuracionesBase = CarSiaConfig::with('accionVencimiento')->get();

            return view('certificados.operaciones.index', compact(
                'operaciones', 'aniosDisponibles', 'bloquesDisponibles', 'bloqueActivo',
                'kpi', 'tiposAlerta', 'tipos', 'historialBloque', 'configuracionesBase',
                'operacionesConfiguradas', 'configuracionesMasivas', 'alertasBloqueActivo', 'tiposBloqueActivo'
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

            // ==============================================================================
            // CONSULTA PARA LA PÍLDORA DESPLEGABLE (Historial Sutil del Cliente)
            // ==============================================================================
            $operacionesDelTercero = CarSiaOperacion::where('id_tercero', $operacion->id_tercero)
                ->select('id', 'numero_radicado', 'numero_bloque', 'id_tercero')
                ->orderBy('created_at', 'desc')
                ->take(8) // Limitamos a 8 para mantener el dropdown sutil y rápido
                ->get();
            // ==============================================================================

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
                        $fechaV = Carbon::parse($factura->fecha_venci);
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
            $tiposCertificados = $tipos;

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

            // ==============================================================================
            // CONSULTAMOS LAS REGLAS BASE ACTIVAS PARA EL MODAL DE CREAR
            // ==============================================================================
            $configuracionesBase = CarSiaConfig::with('accionVencimiento')
                                    ->where('estado_activo', 1)
                                    ->get();


            // ==============================================================================
            // NUEVO: CONSULTAMOS LAS MAESTRAS PARA EL MODAL DE ACTUALIZAR TERCERO
            // ==============================================================================
            $distritos = MaeDistritos::orderBy('COD_DIST', 'asc')->get();
            $maeTipos = MaeTipo::all();
            $congregaciones = MaeCongregacion::orderBy('codigo', 'asc')->get();

            // 3. ENVIAR LAS VARIABLES AL COMPACT (AGREGAMOS $operacionesDelTercero)
            return view('certificados.operaciones.show', compact(
                'operacion', 'lineasUnicas', 'historialEstados', 'historialTipos', 'historialAlertas', 'estados', 'tipos', 'tiposAlerta', 'lineasAgrupadas', 'logsAuditoria', 'operariosData', 'operacionesConfiguradas', 'configuracionesBase',
                'distritos', 'maeTipos', 'congregaciones','tiposCertificados', 'operacionesDelTercero'
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
         *
         * Determina el tipo de certificado que aplica a una operación (individual o global)
         * y genera una firma única (hash) que servirá como identificador de versión
         * para la auditoría y trazabilidad de los documentos generados.
         *
         * @param int $operacionId ID de la operación actual.
         * @param string|int $numeroBloque Lote o bloque de ejecución.
         * @param int|null $timestamp Marca de tiempo (opcional, por defecto now()).
         * @return array Arreglo con el id_tipo resuelto, el hash generado y el usuario responsable.
         */
        private function obtenerDatosAuditoria($operacionId, $numeroBloque, $timestamp = null)
        {
            $timestamp = $timestamp ?? now()->timestamp;

            // Prioridad 1: Buscar si la operación tiene un tipo asignado específicamente a ella.
            $tipoOperacion = CarSiaTipoOperacion::where('id_car_sia_operaciones', $operacionId)
                ->latest()
                ->first();

            // Prioridad 2: Fallback. Si no tiene tipo individual, hereda el tipo global asignado al bloque.
            if (!$tipoOperacion) {
                $tipoOperacion = CarSiaTipoOperacion::where('numero_bloque', $numeroBloque)
                    ->whereNull('id_car_sia_operaciones')
                    ->latest()
                    ->first();
            }

            // Resolución del tipo y construcción de la firma única (Hash)
            $id_tipo = $tipoOperacion ? $tipoOperacion->id_car_sia_tipos : 'N/A';
            $hash = "API-{$numeroBloque}-TIPO-{$id_tipo}-OP-{$operacionId}-TS-{$timestamp}";

            return [
                'id_tipo' => $id_tipo !== 'N/A' ? $id_tipo : null,
                'hash'    => $hash,
                'user_id' => Auth::id()
            ];
        }

        /**
         * 9. GENERACIÓN MASIVA
         *
         * Procesa lotes completos de facturas aplicando reglas de calificación por mora
         * DINÁMICAS (vía JSON), optimizado para grandes volúmenes mediante "chunks",
         * pre-carga de relaciones en memoria e inserciones masivas (upsert).
         *
         * @param Request $request
         * @return \Illuminate\Http\RedirectResponse
         */
        public function generarMasivo(Request $request)
        {
            $bloque = $request->input('bloque') ?? $request->input('numero_bloque');
            $id_car_sia_tipos = $request->input('id_car_sia_tipos');

            // Validaciones de entrada temprana (Early Returns)
            if (!$bloque) {
                return back()->with('error', 'Debe seleccionar un lote (bloque) válido para procesar.');
            }

            if (!$id_car_sia_tipos) {
                return back()->with('error', 'Debe seleccionar un tipo de certificado.');
            }

            try {
                // Iniciar transacción: Si algo falla, ningún registro masivo queda a medias.
                DB::beginTransaction();

                $ahora = now();
                $timestamp = $ahora->timestamp;
                $id_user = Auth::id();
                $totalOperacionesProcesadas = 0;

                // 1. Registrar evento global: Define de qué tipo será este bloque masivo.
                CarSiaTipoOperacion::create([
                    'id_car_sia_operaciones' => null,
                    'numero_bloque'          => $bloque,
                    'id_car_sia_tipos'       => $id_car_sia_tipos,
                    'id_user'                => $id_user,
                ]);

                $id_tipo_global = $id_car_sia_tipos;

                // 2. Procesamiento por lotes (Chunks) de 500 para no saturar la memoria RAM.
                CarSiaOperacion::where('numero_bloque', $bloque)
                    ->chunkById(500, function ($operacionesChunk) use ($ahora, $timestamp, $id_user, $id_tipo_global, $bloque, &$totalOperacionesProcesadas) {

                        // Extracción masiva de IDs para hacer menos consultas a la base de datos
                        $tercerosIds = $operacionesChunk->pluck('id_tercero')->toArray();
                        $operacionesIds = $operacionesChunk->pluck('id')->toArray();
                        $operacionesMap = $operacionesChunk->keyBy('id_tercero');

                        // 3A. Pre-carga en memoria (Eager Loading manual) de los tipos individuales
                        $tiposIndividuales = CarSiaTipoOperacion::whereIn('id_car_sia_operaciones', $operacionesIds)
                            ->orderBy('created_at', 'desc')
                            ->get()
                            ->groupBy('id_car_sia_operaciones');

                        // 3B. ¡NUEVO! Pre-carga masiva en memoria de las CONFIGURACIONES (JSON)
                        // Extraemos todas las reglas activas de las operaciones de este bloque de una sola vez
                        $configuracionesMasivas = \Illuminate\Support\Facades\DB::table('car_sia_operaciones_config')
                            ->whereIn('id_car_sia_operaciones', $operacionesIds)
                            ->where('estado_activo', 1)
                            ->where(function($query) {
                                $query->whereNull('vigente_hasta')
                                      ->orWhere('vigente_hasta', '>=', now());
                            })
                            ->get()
                            ->groupBy('id_car_sia_operaciones'); // Agrupamos por ID de operación

                        // Extraer todas las facturas de este chunk de terceros
                        $facturasChunk = CarSiaApi::where('numero_bloque', $bloque)
                            ->whereIn('tercero', $tercerosIds)
                            ->get();

                        $lineasAInsertar = [];

                        // 4. Bucle principal de evaluación de reglas de negocio
                        foreach ($facturasChunk as $factura) {
                            $operacion = $operacionesMap[$factura->tercero] ?? null;

                            if (!$operacion) continue;

                            // Cálculo de mora
                            $diasMora = 0;
                            if ($factura->fecha_venci) {
                                $fechaVencimiento = Carbon::parse($factura->fecha_venci);
                                $diferencia = $ahora->diffInDays($fechaVencimiento, false);
                                $diasMora = $diferencia < 0 ? abs((int)$diferencia) : 0;
                            }

                            // -----------------------------------------------------------------
                            // 5. MOTOR DINÁMICO (Masivo)
                            // -----------------------------------------------------------------
                            $configsOperacion = $configuracionesMasivas->get($operacion->id, collect());
                            $reglas = [];

                            // Desglosamos el JSON de las configuraciones exclusivas de esta operación
                            foreach ($configsOperacion as $config) {
                                $parametros = is_string($config->parametros) ? json_decode($config->parametros, true) : $config->parametros;
                                
                                if (is_array($parametros)) {
                                    $parametros['_json_original'] = is_string($config->parametros) 
                                        ? $config->parametros 
                                        : json_encode($config->parametros, JSON_UNESCAPED_UNICODE);
                                        
                                    $reglas[] = $parametros;
                                }
                            }

                            // Ordenamos las reglas de menor a mayor exigencia
                            usort($reglas, function($a, $b) {
                                return ($a['mora_dias_max'] ?? 0) <=> ($b['mora_dias_max'] ?? 0);
                            });

                            $calificacion = 'Indefinido';
                            $observacion = '';
                            $metadataRegla = null;

                            if (count($reglas) > 0) {
                                $reglaAplicada = null;

                                foreach ($reglas as $regla) {
                                    $maxMora = (int)($regla['mora_dias_max'] ?? 0);
                                    if ($diasMora <= $maxMora) {
                                        $reglaAplicada = $regla;
                                        break; 
                                    }
                                }

                                if (!$reglaAplicada) {
                                    $reglaAplicada = end($reglas);
                                }

                                $calificacionJSON = $reglaAplicada['clasificacion_mora'] ?? 'Indefinido';
                                $calificacion = ucfirst(strtolower($calificacionJSON)); 
                                $observacion = $reglaAplicada['observacion_fase'] ?? 'Sin observación configurada.';
                                $metadataRegla = $reglaAplicada['_json_original']; // Exacto de la BD

                            } else {
                                // Fallback para operaciones sin reglas en el lote
                                $calificacion = match(true) {
                                    $diasMora > 60 => 'Irregular',
                                    $diasMora > 30 => 'Regular',
                                    default => 'Bueno'
                                };
                                $observacion = "Calificación estándar generada por el sistema debido a $diasMora días de mora (Sin reglas activas).";
                                
                                $metadataRegla = json_encode([
                                    "dias_gracia" => 0,
                                    "mora_dias_max" => 0,
                                    "requiere_accion" => false,
                                    "observacion_fase" => $observacion,
                                    "bloqueo_automatico" => false,
                                    "clasificacion_mora" => strtolower($calificacion),
                                    "notificacion_gerencia" => false,
                                    "incluir_historico_3_anos" => false
                                ], JSON_UNESCAPED_UNICODE);
                            }
                            // -----------------------------------------------------------------

                            // Determinar el tipo final (Individual tiene preferencia sobre el Global)
                            $id_tipo_final = isset($tiposIndividuales[$operacion->id])
                                ? $tiposIndividuales[$operacion->id]->first()->id_car_sia_tipos
                                : $id_tipo_global;

                            // Generar hash para esta línea específica
                            $hash_certificado = "API-{$bloque}-TIPO-{$id_tipo_final}-OP-{$operacion->id}-TS-{$timestamp}";

                            // Preparar arreglo para inserción masiva
                            $lineasAInsertar[] = [
                                'id_car_sia_operaciones' => $operacion->id,
                                'id_factura'             => $factura->id,
                                'id_car_sia_lineas'      => $factura->cuenta,
                                'numero_bloque'          => $bloque,
                                'observacion'            => $observacion,       // <--- AHORA VIENE DEL JSON
                                'calificacion'           => $calificacion,      // <--- AHORA VIENE DEL JSON
                                'metadata'               => $metadataRegla,     // <--- AHORA GUARDA EL JSON EXACTO
                                'fecha_venci'            => $factura->fecha_venci,
                                'id_car_sia_estados'     => 3,
                                'dias_mora_automaticos'  => $diasMora,
                                'procesado_en'           => $ahora->format('Y-m-d H:i:s'),
                                'id_user'                => $id_user,
                                'id_car_sia_tipos'       => $id_tipo_final !== 'N/A' ? $id_tipo_final : null,
                                'hash_certificado'       => $hash_certificado,
                            ];
                        }

                        // 6. Inserción Masiva (Upsert) en sub-lotes de 1000
                        if (!empty($lineasAInsertar)) {
                            collect($lineasAInsertar)->chunk(1000)->each(function ($batch) {
                                CarSiaOperacionLinea::upsert(
                                    $batch->toArray(),
                                    // Llaves únicas: Si coinciden, actualiza. Si no (ej: nuevo hash), inserta.
                                    ['id_car_sia_operaciones', 'id_factura', 'hash_certificado'],
                                    // Columnas que se actualizarán si hay coincidencia (AGREGAR METADATA AQUÍ)
                                    [
                                        'id_car_sia_lineas', 'numero_bloque', 'observacion', 'calificacion', 'metadata',
                                        'fecha_venci', 'id_car_sia_estados', 'dias_mora_automaticos', 'procesado_en',
                                        'id_user', 'id_car_sia_tipos'
                                    ]
                                );
                            });
                        }

                        $totalOperacionesProcesadas += $operacionesChunk->count();
                    });

                // 7. Registro en Log de Auditoría (Fuera del bucle chunk para hacerlo 1 sola vez)
                $tipo = \App\Models\Certificados\CarSiaTipo::find($id_car_sia_tipos);
                $this->registrarLogAuditoria(
                    $bloque, 1, 3,
                    'Generación masiva de certificados', 'Bloque', 'Creación en lote de nuevos hashes aplicando reglas dinámicas JSON.',
                    [],
                    ['registros_afectados' => $totalOperacionesProcesadas],
                    ['tipo_asignado' => $tipo ? $tipo->nombre : 'Desconocido']
                );

                DB::commit();

                return back()->with('success', "Procesamiento masivo completado: Lote $bloque procesado exitosamente aplicando matriz de reglas dinámicas ($totalOperacionesProcesadas operaciones).");

            } catch (\Exception $e) {
                DB::rollBack();
                \Illuminate\Support\Facades\Log::error("Error en procesamiento masivo: " . $e->getMessage() . " en la línea " . $e->getLine());
                return back()->with('error', 'Ocurrió un error en la base de datos: ' . $e->getMessage());
            }
        }

        /**
         * 10. VISOR INDIVIDUAL (Modo Lectura)
         *
         * Controlador diseñado para cargar el PDF en un iframe.
         * Es "idempotente": NO altera la base de datos, solo consulta y renderiza
         * basándose en un hash específico para garantizar integridad documental.
         *
         * @param Request $request
         * @param int $id ID de la operación
         * @return \Illuminate\Http\Response
         */
        public function generarIndividual(Request $request, $id)
        {
            try {
                $operacion = CarSiaOperacion::with('tercero')->findOrFail($id);

                // 1. Obtener el hash solicitado
                $hashFiltro = $request->query('hash');

                if (!$hashFiltro) {
                    // Quitamos la dependencia del numero_bloque para evitar vacíos en individuales
                    $hashFiltro = CarSiaOperacionLinea::where('id_car_sia_operaciones', $operacion->id)
                        ->orderBy('created_at', 'desc')
                        ->value('hash_certificado');
                }

                // 2. Extraer exactamente las líneas asociadas a esa versión (Hash)
                $lineas = CarSiaOperacionLinea::where('id_car_sia_operaciones', $operacion->id)
                    ->where('hash_certificado', $hashFiltro)
                    ->get();

                if ($lineas->isEmpty()) {
                    // Retornar texto plano o HTML simple. NUNCA un abort() que lance una vista 404 grande en el iframe.
                    return response('<div style="font-family:sans-serif; text-align:center; padding: 20px; color:#666;">No hay datos procesados para generar este certificado.</div>', 404);
                }

                // 3. Capturar el tipo de certificado (Prioridad al enviado por la Request desde Blade)
                $tipoCertificadoId = $request->query('tipo_id') ?? $lineas->first()->id_car_sia_tipos;

                // 4. Asignar dinámicamente la vista
                $vistaPdf = match((int) $tipoCertificadoId) {
                    1 => 'certificados.pdf.inicio',
                    2 => 'certificados.pdf.paz_y_salvo',
                    3 => 'certificados.pdf.cobro_persuasivo',
                    4 => 'certificados.pdf.estado_cuenta',
                    5 => 'certificados.pdf.acuerdos_pago',
                    default => 'certificados.pdf.paz_y_salvo',
                };

                // 5. Renderizar vista a PDF
                $pdf = Pdf::loadView($vistaPdf, compact('operacion', 'lineas'));
                
                return $pdf->stream("Certificado_{$operacion->numero_radicado}.pdf");

            } catch (\Exception $e) {
                Log::error("Error al renderizar certificado: " . $e->getMessage());
                // NUNCA hacer return back() dentro del contexto de un iframe. 
                return response('<div style="font-family:sans-serif; text-align:center; padding: 20px; color:red;">Ocurrió un error al mostrar el certificado: ' . $e->getMessage() . '</div>', 500);
            }
        }


        // =========================================================================
        // INICIO PROCESAMIENTO INDIVIDUAL Y ACTUALIZACION
        // =========================================================================

        /**
         * 11. MOTOR DE REGLAS INTERNO (Para procesamiento 1 a 1)
         *
         * Extrae parámetros de calificación desde la tabla car_sia_operaciones_config
         * leyendo el campo JSON para procesar las líneas dinámicamente y hereda
         * la configuración EXACTA en el campo metadata.
         *
         * @param CarSiaOperacion $operacion Modelo de la operación a procesar.
         * @return void
         */
        private function procesarLineasOperacion($operacion)
        {
            // 1. Filtro optimizado sobre car_sia_api
            $facturas = CarSiaApi::where('numero_bloque', $operacion->numero_bloque)
                ->where('tercero', $operacion->id_tercero)
                ->get();

            // Obtenemos los metadatos para la firma de versión
            $auditoria = $this->obtenerDatosAuditoria($operacion->id, $operacion->numero_bloque);

            // 2. EXTRAER Y PREPARAR REGLAS DINÁMICAS DESDE EL JSON
            $configuraciones = \Illuminate\Support\Facades\DB::table('car_sia_operaciones_config')
                ->where('id_car_sia_operaciones', $operacion->id)
                ->where('estado_activo', 1)
                ->where(function($query) {
                    $query->whereNull('vigente_hasta')
                          ->orWhere('vigente_hasta', '>=', now());
                })
                ->get();

            $reglas = [];
            foreach ($configuraciones as $config) {
                $parametros = is_string($config->parametros) ? json_decode($config->parametros, true) : $config->parametros;
                
                if (is_array($parametros)) {
                    // Guardamos el string original intacto para no alterar la estructura JSON
                    $parametros['_json_original'] = is_string($config->parametros) 
                        ? $config->parametros 
                        : json_encode($config->parametros, JSON_UNESCAPED_UNICODE);
                        
                    $reglas[] = $parametros;
                }
            }

            // Ordenamos las reglas de menor a mayor exigencia (por mora_dias_max)
            usort($reglas, function($a, $b) {
                return ($a['mora_dias_max'] ?? 0) <=> ($b['mora_dias_max'] ?? 0);
            });


            // 3. PROCESAMIENTO DE FACTURAS
            foreach ($facturas as $factura) {
                
                $diasMora = 0;
                if ($factura->fecha_venci) {
                    $fechaVencimiento = Carbon::parse($factura->fecha_venci);
                    $diferencia = now()->diffInDays($fechaVencimiento, false);
                    $diasMora = $diferencia < 0 ? abs((int)$diferencia) : 0;
                }

                // 4. EVALUACIÓN CON EL MOTOR DE REGLAS DINÁMICO (JSON)
                $calificacion = 'Indefinido';
                $observacion = '';
                $metadataRegla = null;

                if (count($reglas) > 0) {
                    $reglaAplicada = null;

                    foreach ($reglas as $regla) {
                        $maxMora = (int)($regla['mora_dias_max'] ?? 0);
                        if ($diasMora <= $maxMora) {
                            $reglaAplicada = $regla;
                            break; 
                        }
                    }

                    if (!$reglaAplicada) {
                        $reglaAplicada = end($reglas);
                    }

                    // Asignación de Calificación
                    $calificacionJSON = $reglaAplicada['clasificacion_mora'] ?? 'Indefinido';
                    $calificacion = ucfirst(strtolower($calificacionJSON)); 
                    
                    // Asignación de Observación: Toma exclusivamente el texto del JSON
                    $observacion = $reglaAplicada['observacion_fase'] ?? 'Sin observación configurada.';

                    // METADATA EXACTA: Pasamos el JSON tal cual vino de la base de datos
                    $metadataRegla = $reglaAplicada['_json_original'];

                } else {
                    // Fallback en caso de que no haya reglas asignadas
                    $calificacion = match(true) {
                        $diasMora > 60 => 'Irregular',
                        $diasMora > 30 => 'Regular',
                        default => 'Bueno'
                    };
                    $observacion = "Calificación estándar generada por el sistema debido a $diasMora días de mora (Sin reglas activas).";
                    
                    // Genera un JSON manteniendo estrictamente TU estructura requerida
                    $metadataRegla = json_encode([
                        "dias_gracia" => 0,
                        "mora_dias_max" => 0,
                        "requiere_accion" => false,
                        "observacion_fase" => $observacion,
                        "bloqueo_automatico" => false,
                        "clasificacion_mora" => strtolower($calificacion),
                        "notificacion_gerencia" => false,
                        "incluir_historico_3_anos" => false
                    ], JSON_UNESCAPED_UNICODE);
                }

                // Guardar / Actualizar versión de la línea
                CarSiaOperacionLinea::updateOrCreate(
                    [
                        'id_car_sia_operaciones' => $operacion->id,
                        'numero_bloque'          => $operacion->numero_bloque,
                        'id_factura'             => $factura->id,
                        'hash_certificado'       => $auditoria['hash'],
                    ],
                    [
                        'id_car_sia_lineas'      => $factura->cuenta,
                        'observacion'            => $observacion,      // <- Texto normal extraído del JSON
                        'calificacion'           => $calificacion,
                        'metadata'               => $metadataRegla,    // <- Estructura JSON exacta requerida
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
         * 11.5 PROCESAR INDIVIDUALMENTE (El "Jefe" del motor interno)
         *
         * Recibe la petición del modal, registra el tipo de certificado seleccionado,
         * y ejecuta el motor interno para procesar y guardar las líneas.
         *
         * @param Request $request
         * @param int $id
         * @return \Illuminate\Http\RedirectResponse
         */
        public function procesarIndividual(Request $request, $id)
        {
            $request->validate([
                'tipo_certificado_id' => 'required' // <--- Debe coincidir con el name del HTML
            ]);

            try {
                DB::beginTransaction();

                $operacion = CarSiaOperacion::findOrFail($id);
                $tipoId = $request->input('tipo_certificado_id'); // <--- Recibimos el ID

                // 1. Registramos el tipo (Lo que antes hacía asignar_tipo)
                \App\Models\Certificados\CarSiaTipoOperacion::create([
                    'id_car_sia_operaciones' => $operacion->id,
                    'numero_bloque'          => $operacion->numero_bloque,
                    'id_car_sia_tipos'       => $tipoId,
                    'id_user'                => Auth::id(),
                ]);

                // 2. Ejecutamos tu motor interno
                $this->procesarLineasOperacion($operacion);

                DB::commit();

                return back()->with('success', 'Certificado procesado y generado exitosamente.');

            } catch (\Exception $e) {
                DB::rollBack();
                return back()->with('error', 'Ocurrió un error: ' . $e->getMessage());
            }
        }


        /**
         * 12. ACTUALIZAR LÍNEAS DESDE VISTA HOJA DE CÁLCULO
         *
         * Recibe ediciones manuales del usuario. Aplica el patrón de "Inmutabilidad Parcial":
         * En lugar de sobrescribir el registro original, replica la línea (nueva versión)
         * asignándole el nuevo hash, manteniendo intacta la historia previa.
         *
         * @param Request $request
         * @param int $id ID de la operación
         * @return \Illuminate\Http\RedirectResponse
         */
        public function actualizarLineas(Request $request, $id)
        {
            // Validaciones estrictas de los arrays que provienen del frontend
            $request->validate([
                'lineas'                               => 'required|array',
                'lineas.*.calificacion'                => 'required|string',
                'lineas.*.id_car_sia_estados'          => 'nullable|exists:car_sia_estados,id',
                'lineas.*.dias_mora_automaticos'       => 'required|numeric',
                'lineas.*.fecha_venci'                 => 'nullable|date',
                'lineas.*.fecha_ultimo_recordatorio'   => 'nullable|date',
                'lineas.*.procesado_en'                => 'nullable|date',
                'lineas.*.observacion'                 => 'nullable|string',
                'tipo_certificado_id'                  => 'nullable|exists:car_sia_tipos,id',
                'dias_gracia_lote'                     => 'nullable|integer|min:0' // Validación del input oculto
            ]);

            try {
                DB::transaction(function () use ($request, $id) {
                    $operacion = CarSiaOperacion::findOrFail($id);
                    $auditoria = $this->obtenerDatosAuditoria($operacion->id, $operacion->numero_bloque);

                    // OPTIMIZACIÓN DE MEMORIA
                    $lineasIds = array_keys($request->lineas);
                    $lineasOriginales = CarSiaOperacionLinea::whereIn('id', $lineasIds)
                        ->where('id_car_sia_operaciones', $operacion->id)
                        ->where('numero_bloque', $operacion->numero_bloque)
                        ->get()
                        ->keyBy('id');

                    if ($lineasOriginales->count() !== count($lineasIds)) {
                        throw new \Exception("Una o más líneas no pertenecen a la operación actual o no existen.");
                    }

                    // Capturar días de gracia general para el lote
                    $nuevosDiasGracia = $request->filled('dias_gracia_lote') ? (int) $request->dias_gracia_lote : null;

                    foreach ($request->lineas as $lineaId => $data) {
                        $lineaOriginal = $lineasOriginales[$lineaId];

                        // PATRÓN DE VERSIONADO: Replicamos la entidad
                        $nuevaLinea = $lineaOriginal->replicate();

                        // ------------------------------------------------------------------
                        // SOLUCIÓN JSON: Tratamiento de la columna 'metadata'
                        // ------------------------------------------------------------------
                        $metadataArray = [];
                        
                        // Como el modelo tiene el cast a 'array', $lineaOriginal->metadata ya es un arreglo (o null)
                        if (!empty($lineaOriginal->metadata)) {
                            $metadataArray = is_string($lineaOriginal->metadata) ? json_decode($lineaOriginal->metadata, true) : $lineaOriginal->metadata;
                        }

                        // Actualizar SOLAMENTE dias_gracia si se escribió algo en el modal
                        if ($nuevosDiasGracia !== null) {
                            $metadataArray['dias_gracia'] = $nuevosDiasGracia;
                        }

                        // Forzar el tipado correcto de todas las variables del JSON para evitar los strings "1" o "0"
                        if (!empty($metadataArray)) {
                            $metadataArray['dias_gracia'] = isset($metadataArray['dias_gracia']) ? (int) $metadataArray['dias_gracia'] : 0;
                            $metadataArray['mora_dias_max'] = isset($metadataArray['mora_dias_max']) ? (int) $metadataArray['mora_dias_max'] : 0;
                            
                            $metadataArray['requiere_accion'] = isset($metadataArray['requiere_accion']) ? filter_var($metadataArray['requiere_accion'], FILTER_VALIDATE_BOOLEAN) : false;
                            $metadataArray['bloqueo_automatico'] = isset($metadataArray['bloqueo_automatico']) ? filter_var($metadataArray['bloqueo_automatico'], FILTER_VALIDATE_BOOLEAN) : false;
                            $metadataArray['notificacion_gerencia'] = isset($metadataArray['notificacion_gerencia']) ? filter_var($metadataArray['notificacion_gerencia'], FILTER_VALIDATE_BOOLEAN) : false;
                            $metadataArray['incluir_historico_3_anos'] = isset($metadataArray['incluir_historico_3_anos']) ? filter_var($metadataArray['incluir_historico_3_anos'], FILTER_VALIDATE_BOOLEAN) : false;
                        }

                        // Asignar el array directamente al campo correcto (Laravel lo convierte a JSON automáticamente por el cast)
                        $nuevaLinea->metadata = empty($metadataArray) ? null : $metadataArray;
                        // ------------------------------------------------------------------

                        // Asignación de datos restantes
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
                    }

                    // Registro de la acción en la bitácora de auditoría
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
                Log::error("Error actualizando líneas: " . $e->getMessage());
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

            // Traemos los JSON base una sola vez
            $configuracionesBase = CarSiaConfig::whereIn('id', $request->id_car_sia_config)
                                        ->get()
                                        ->keyBy('id');

            foreach ($request->id_car_sia_config as $idConfig) {
                // Capturamos el JSON de esta configuración base en específico
                $parametrosBase = $configuracionesBase->has($idConfig) ? $configuracionesBase[$idConfig]->parametros : [];
                $parametrosJson = is_array($parametrosBase) ? json_encode($parametrosBase) : $parametrosBase;

                $registros[] = [
                    'numero_bloque'          => $request->numero_bloque,
                    'id_car_sia_operaciones' => null,
                    'id_car_sia_config'      => $idConfig,
                    'parametros'             => $parametrosJson,  // <-- AQUÍ SE GUARDA LA "FOTOGRAFÍA" JSON
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

            //Traemos los JSON base una sola vez
            $configuracionesBase = CarSiaConfig::whereIn('id', $request->id_car_sia_config)
                                        ->get()
                                        ->keyBy('id');

            DB::beginTransaction();
            try {
                $contadorOps = 0;
                $totalReglasAsignadas = 0;
                $ahora = now();

                $query->select('id')->chunk(500, function ($operaciones) use ($request, $estado, $ahora, $idUser, $configuracionesBase, &$contadorOps, &$totalReglasAsignadas) {
                    $registrosBatch = [];

                    foreach ($operaciones as $op) {
                        $contadorOps++;
                        foreach ($request->id_car_sia_config as $idConfig) {

                            // Capturamos el JSON de esta configuración base en específico
                            $parametrosBase = $configuracionesBase->has($idConfig) ? $configuracionesBase[$idConfig]->parametros : [];
                            $parametrosJson = is_array($parametrosBase) ? json_encode($parametrosBase) : $parametrosBase;

                            $registrosBatch[] = [
                                'numero_bloque'          => $request->numero_bloque,
                                'id_car_sia_operaciones' => $op->id,
                                'id_car_sia_config'      => $idConfig,
                                'parametros'             => $parametrosJson,  // <-- AQUÍ SE GUARDA LA "FOTOGRAFÍA" JSON
                                'estado_notificacion'    => $estado,
                                'id_user'                => $idUser,
                                'justificacion'          => $request->justificacion,
                                'vigente_hasta'          => $request->vigente_hasta,
                                'estado_activo'          => 1,
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

            // Traemos los JSON base una sola vez
            $configuracionesBase = CarSiaConfig::whereIn('id', $request->id_car_sia_config)
                                        ->get()
                                        ->keyBy('id');

            foreach ($request->id_car_sia_config as $idConfig) {
                // Capturamos el JSON de esta configuración base en específico
                $parametrosBase = $configuracionesBase->has($idConfig) ? $configuracionesBase[$idConfig]->parametros : [];
                $parametrosJson = is_array($parametrosBase) ? json_encode($parametrosBase) : $parametrosBase;

                $registros[] = [
                    'numero_bloque'          => $operacion->numero_bloque,
                    'id_car_sia_operaciones' => $operacion->id,
                    'id_car_sia_config'      => $idConfig,
                    'parametros'             => $parametrosJson,  // <-- AQUÍ SE GUARDA LA "FOTOGRAFÍA" JSON
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

        /**
         * 4. ACTUALIZAR PARÁMETROS JSON, JUSTIFICACIÓN Y ESTADO DE UNA REGLA ASIGNADA
         */
        public function updateParametrosJson(Request $request, $id)
        {
            $request->validate([
                'parametros'          => 'required|array',
                'justificacion'       => 'nullable|string', // <-- ESTO FALTABA
                'vigente_hasta'       => 'nullable|date',
                'estado_notificacion' => 'required|boolean',
                'estado_activo'       => 'required|boolean',
            ]);

            try {
                $config = CarSiaOperacionConfig::findOrFail($id);

                // Guardar parámetros. Laravel convierte el array a JSON automáticamente por el $casts
                $config->parametros = $request->parametros;
                $config->justificacion = $request->justificacion; // <-- ESTO FALTABA
                $config->vigente_hasta = $request->vigente_hasta;
                $config->estado_notificacion = $request->estado_notificacion;
                $config->estado_activo = $request->estado_activo;

                // Actualizamos el usuario que hizo la última modificación lógica
                $config->id_user = Auth::id();
                $config->save();

                // Registrar auditoría del cambio lógico
                $this->registrarLogAuditoria(
                    $config->numero_bloque, 1, 15, // Asumiendo ID de evento de configuración
                    'Actualización de Lógica JSON', 'Configuración', 'Se editaron los parámetros lógicos de una regla previamente asignada.',
                    ['id_operacion' => $config->id_car_sia_operaciones, 'id_config' => $config->id],
                    [],
                    [],
                    ['parametros_nuevos' => json_encode($request->parametros), 'justificacion' => $request->justificacion]
                );

                return back()->with('success', 'Parámetros lógicos actualizados correctamente.');
            } catch (\Exception $e) {
                return back()->with('error', 'Error al actualizar los parámetros: ' . $e->getMessage());
            }
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
            'nom1'     => 'required|string|max:50',
            'nom2'     => 'nullable|string|max:50',
            'apl1'     => 'required|string|max:50',
            'apl2'     => 'nullable|string|max:50',
            'tel'      => 'nullable|string|max:20',
            'tel1'     => 'nullable|string|max:20',
            'dir'      => 'nullable|string|max:150',
            'email'    => 'nullable|email|max:100',
            'cod_dist' => 'nullable|string|max:50',
            'tip_prv'  => 'nullable|string|max:50',
            'congrega' => 'nullable|string|max:50',
        ]);

        try {
            $operacion = CarSiaOperacion::with('tercero')->findOrFail($id);
            $tercero = $operacion->tercero;

            if (!$tercero) {
                return redirect()->back()->with('error', 'No se encontró el tercero en las maestras.');
            }

            // Prevención de errores en PHP 8.1+ al aplicar trim() o strtoupper() a valores nulos usando "?? ''"
            $nom1 = mb_strtoupper(trim($request->nom1), 'UTF-8');
            $nom2 = mb_strtoupper(trim($request->nom2 ?? ''), 'UTF-8');
            $apl1 = mb_strtoupper(trim($request->apl1), 'UTF-8');
            $apl2 = mb_strtoupper(trim($request->apl2 ?? ''), 'UTF-8');

            $nombreCompleto = "{$nom1} {$nom2} {$apl1} {$apl2}";
            $nombreConcatenado = trim(preg_replace('/\s+/', ' ', $nombreCompleto));

            $tercero->update([
                'nom1'     => $nom1,
                'nom2'     => $nom2,
                'apl1'     => $apl1,
                'apl2'     => $apl2,
                'nom_ter'  => $nombreConcatenado,
                // Si el campo viene vacío desde el select o el input, forzamos un null real en BD
                'tel'      => $request->filled('tel') ? trim($request->tel) : null,
                'tel1'     => $request->filled('tel1') ? trim($request->tel1) : null,
                'dir'      => $request->filled('dir') ? trim($request->dir) : null,
                'email'    => $request->filled('email') ? trim($request->email) : null,
                'cod_dist' => $request->filled('cod_dist') ? trim($request->cod_dist) : null,
                'tip_prv'  => $request->filled('tip_prv') ? trim($request->tip_prv) : null,
                'congrega' => $request->filled('congrega') ? trim($request->congrega) : null,
            ]);

            return redirect()->back()->with('success', 'Datos del cliente actualizados y concatenados correctamente.');

        } catch (\Exception $e) {
            Log::error("Error actualizando tercero desde operaciones: " . $e->getMessage());
            return redirect()->back()->with('error', 'Ocurrió un error al actualizar los datos del cliente.');
        }
    }

}
