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

class OperacionController extends Controller
{
    /**
     * 1. GESTIÓN MATRIZ: Listar el motor de operaciones aislado por LOTES
     */
    public function index(Request $request)
    {
        try {
            $bloquesDisponibles = Cache::remember('sia_bloques_disponibles', 300, function () {
                return CarSiaOperacion::whereNotNull('numero_bloque')
                    ->select('numero_bloque', DB::raw('MAX(created_at) as fecha_ejecucion'))
                    ->groupBy('numero_bloque')
                    ->orderBy('fecha_ejecucion', 'desc')
                    ->get();
            });

            $bloqueActivo = $request->input('bloque', $bloquesDisponibles->first()?->numero_bloque);

            $kpi = [
                'total'      => 0,
                'procesados' => 0,
                'pendientes' => 0,
            ];

            $historialBloque = collect();

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
            }

            $query = CarSiaOperacion::with([
                'tercero',
                'lineas.factura',
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
                      ->orWhere('id_tercero', 'LIKE', "%{$search}%");
                });
            }

            $operaciones = $query->orderBy('created_at', 'desc')->paginate(20)->withQueryString();

            $aniosDisponibles = Cache::remember('sia_anios_disponibles', 3600, function () {
                return CarSiaOperacion::whereNotNull('created_at')
                    ->selectRaw('YEAR(created_at) as anio')
                    ->groupBy('anio')
                    ->orderBy('anio', 'desc')
                    ->pluck('anio');
            });

            $tiposAlerta = CarSiaTipoAlerta::all();

            return view('certificados.operaciones.index', compact(
                'operaciones',
                'aniosDisponibles',
                'bloquesDisponibles',
                'bloqueActivo',
                'kpi',
                'tiposAlerta',
                'historialBloque'
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
            // 1. Modificamos la carga de las 'lineas' para ordenarlas de la más nueva a la más vieja
            $operacion = CarSiaOperacion::with([
                'tercero',
                'lineas' => function($query) {
                    $query->orderBy('created_at', 'desc');
                },
                'lineas.lineaSia',
                'lineas.estadoOperacion',
                'lineas.factura'
            ])->findOrFail($id);

            // 2. Extraemos las líneas únicas por factura.
            $lineasUnicas = $operacion->lineas->unique('id_factura');

            $registrosCrudos = CarSiaApi::with('lineaSia')
                ->where('numero_bloque', $operacion->numero_bloque)
                ->where('tercero', $operacion->id_tercero)
                ->get();

            $lineasAgrupadas = $registrosCrudos->groupBy(function($item) {
                return $item->lineaSia->nombre
                    ?? $item->nombre_cuenta
                    ?? $item->cuenta
                    ?? 'Línea Desconocida';
            });

            $historialEstados = CarSiaEstadoOperacion::with('estado')
                ->where('id_car_sia_operaciones', $operacion->id)
                ->orWhere(function($q) use ($operacion) {
                    $q->where('numero_bloque', $operacion->numero_bloque)
                      ->whereNull('id_car_sia_operaciones');
                })
                ->orderBy('created_at', 'desc')
                ->get();

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
                    $lineaAsociada = $operacion->lineas->where('id_car_sia_tipos', $registro->id_car_sia_tipos)->first();

                    $registro->nombre_user = 'Usuario / Sistema';
                    $registro->cargo_user = '';

                    if ($lineaAsociada && $lineaAsociada->usuario) {
                        $registro->nombre_user = $lineaAsociada->usuario->name;

                        if ($lineaAsociada->usuario->cargoRelation) {
                            $registro->cargo_user = ' / ' . $lineaAsociada->usuario->cargoRelation->nombre_cargo;
                        }
                    }

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

            $estados = CarSiaEstado::all();
            $tipos = CarSiaTipo::all();
            $tiposAlerta = CarSiaTipoAlerta::all();

            return view('certificados.operaciones.show', compact(
                'operacion', 'lineasUnicas', 'historialEstados', 'historialTipos', 'historialAlertas', 'estados', 'tipos', 'tiposAlerta', 'lineasAgrupadas'
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

        CarSiaOperacionAlerta::create([
            'id'                      => (string) Str::uuid(),
            'id_car_sia_tipos_alerta' => $request->id_car_sia_tipos_alerta,
            'numero_bloque'           => $request->numero_bloque,
            'id_car_sia_operaciones'  => null,
            'fecha_programada'        => $request->fecha_programada,
            'id_user'                 => Auth::id(),
        ]);

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
                            $fechaVencimiento = \Carbon\Carbon::parse($factura->fecha_venci);
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

            DB::commit();

            return back()->with('success', "Procesamiento masivo completado: Lote $bloque procesado exitosamente y asignado al tipo seleccionado ($totalOperacionesProcesadas operaciones).");

        } catch (\Exception $e) {
            DB::rollBack();
            \Illuminate\Support\Facades\Log::error("Error en procesamiento masivo: " . $e->getMessage() . " en la línea " . $e->getLine());
            return back()->with('error', 'Ocurrió un error en la base de datos: ' . $e->getMessage());
        }
    }

    /**
     * 10. GENERACIÓN INDIVIDUAL
     */
    public function generarIndividual($id)
    {
        try {
            $operacion = CarSiaOperacion::with('tercero')->findOrFail($id);

            $this->procesarLineasOperacion($operacion);

            // Obtener específicamente las líneas generadas por la iteración más reciente (último hash)
            $ultimoHash = CarSiaOperacionLinea::where('id_car_sia_operaciones', $operacion->id)
                ->orderBy('created_at', 'desc')
                ->value('hash_certificado');

            $lineas = CarSiaOperacionLinea::where('id_car_sia_operaciones', $operacion->id)
                ->where('hash_certificado', $ultimoHash)
                ->get();

            $pdf = Pdf::loadView('certificados.pdf.certificado_aldia', compact('operacion', 'lineas'));
            return $pdf->stream("Certificado_{$operacion->numero_radicado}.pdf");

        } catch (\Exception $e) {
            Log::error("Error al generar certificado individual: " . $e->getMessage());
            return back()->with('error', 'Ocurrió un error al generar el certificado.');
        }
    }

    /**
     * 11. MOTOR DE REGLAS INTERNO (Para procesamiento 1 a 1)
     */
    private function procesarLineasOperacion($operacion)
    {
        $facturas = CarSiaApi::where('numero_bloque', $operacion->numero_bloque)
            ->where('tercero', $operacion->id_tercero)
            ->get();

        // Extraemos Auditoría unificada (genera un Hash nuevo)
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
                    'id_factura'             => $factura->id,
                    // INCLUIR EL HASH AQUÍ GARANTIZA QUE SEA UN INSERT Y NO UN UPDATE
                    'hash_certificado'       => $auditoria['hash'],
                ],
                [
                    'id_car_sia_lineas'      => $factura->cuenta,
                    'numero_bloque'          => $operacion->numero_bloque,
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
     * 12. ACTUALIZAR LÍNEAS DESDE VISTA HOJA DE CÁLCULO
     */
    public function actualizarLineas(Request $request, $id)
    {
        $request->validate([
            'lineas'                         => 'required|array',
            'lineas.*.calificacion'          => 'required|string',
            'lineas.*.dias_mora_automaticos' => 'required|numeric',
            'lineas.*.fecha_venci'           => 'nullable|date',
            'lineas.*.observacion'           => 'nullable|string',
        ]);

        try {
            DB::transaction(function () use ($request, $id) {
                $operacion = CarSiaOperacion::findOrFail($id);

                // Generamos un nuevo Timestamp/Hash para esta revisión manual
                $auditoria = $this->obtenerDatosAuditoria($operacion->id, $operacion->numero_bloque);

                foreach ($request->lineas as $lineaId => $data) {
                    $lineaOriginal = CarSiaOperacionLinea::findOrFail($lineaId);

                    // Clonar la línea original para conservar el historial anterior
                    $nuevaLinea = $lineaOriginal->replicate();

                    // Aplicar las modificaciones
                    $nuevaLinea->calificacion = $data['calificacion'];
                    $nuevaLinea->dias_mora_automaticos = $data['dias_mora_automaticos'];
                    $nuevaLinea->fecha_venci = $data['fecha_venci'];
                    $nuevaLinea->observacion = $data['observacion'] ?? '';

                    // Asignar los nuevos datos de auditoría
                    $nuevaLinea->id_user = $auditoria['user_id'];
                    $nuevaLinea->hash_certificado = $auditoria['hash'];

                    $nuevaLinea->save();
                }
            });

            return redirect()->back()->with('success', 'Cambios guardados. Se ha generado una nueva revisión (Nuevo Hash) conservando la versión anterior.');

        } catch (\Exception $e) {
            Log::error("Error actualizando líneas tipo Excel: " . $e->getMessage());
            return redirect()->back()->with('error', 'Ocurrió un error al intentar guardar los cambios.');
        }
    }
}
