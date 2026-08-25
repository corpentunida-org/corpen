<?php

namespace App\Http\Controllers\Certificados;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

// Agregados para el procesamiento masivo y PDFs
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
use App\Models\Certificados\CarSiaApi; // Importante para leer las facturas
use App\Models\Maestras\MaeTerceros;

class OperacionController extends Controller
{
    /**
     * 1. GESTIÓN MATRIZ: Listar el motor de operaciones aislado por LOTES
     */
    public function index(Request $request)
    {
        try {
            // =================================================================
            // OPTIMIZACIÓN 1: CACHÉ PARA BLOQUES (TTL: 5 Minutos)
            // =================================================================
            $bloquesDisponibles = Cache::remember('sia_bloques_disponibles', 300, function () {
                return CarSiaOperacion::whereNotNull('numero_bloque')
                    ->select('numero_bloque', DB::raw('MAX(created_at) as fecha_ejecucion'))
                    ->groupBy('numero_bloque')
                    ->orderBy('fecha_ejecucion', 'desc')
                    ->get();
            });

            // Fíjate en el signo de interrogación antes de la flecha para evitar fallos si está vacío
            $bloqueActivo = $request->input('bloque', $bloquesDisponibles->first()?->numero_bloque);

            // =================================================================
            // OPTIMIZACIÓN 2: KPIs CON QUERIES NATIVAS
            // =================================================================
            $kpi = [
                'total'      => 0,
                'procesados' => 0,
                'pendientes' => 0,
            ];

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
            }

            // =================================================================
            // OPTIMIZACIÓN 3: LA CARGA DE RELACIONES SE MANTIENE
            // =================================================================
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

            // =================================================================
            // OPTIMIZACIÓN 4: CACHÉ PARA AÑOS (TTL: 60 Minutos)
            // =================================================================
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
                'tiposAlerta'
            ));

        } catch (\Exception $e) {
            dd($e->getMessage(), 'Línea del error: ' . $e->getLine());
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
                'lineas.lineaSia',
                'lineas.estadoOperacion',
                'lineas.factura'
            ])->findOrFail($id);

            // =========================================================
            // LÓGICA MOVIDA AL CONTROLADOR (BUENAS PRÁCTICAS MVC)
            // =========================================================
            // 1. Cargamos los registros crudos incluyendo la relación con la tabla maestra
            $registrosCrudos = CarSiaApi::with('lineaSia')
                ->where('numero_bloque', $operacion->numero_bloque)
                ->where('tercero', $operacion->id_tercero)
                ->get();

            // 2. Agrupamos usando el nombre oficial de la tabla maestra
            $lineasAgrupadas = $registrosCrudos->groupBy(function($item) {
                // Busca primero en la relación maestra, luego en nombre_cuenta, luego en la cuenta misma
                return $item->lineaSia->nombre 
                    ?? $item->nombre_cuenta 
                    ?? $item->cuenta 
                    ?? 'Línea Desconocida';
            });
            // =========================================================

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
                ->get();

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

            // Pasamos la variable $lineasAgrupadas a la vista
            return view('certificados.operaciones.show', compact(
                'operacion', 'historialEstados', 'historialTipos', 'historialAlertas', 'estados', 'tipos', 'tiposAlerta', 'lineasAgrupadas'
            ));

        } catch (\Exception $e) {
            Log::error('🚨 ERROR AL ABRIR EL EXPEDIENTE (SHOW): ' . $e->getMessage());
            return back()->with('error', 'No se pudo cargar el detalle de la operación.');
        }
    }

    /**
     * 3. TRANSICIONA ESTADOS: Registrar un nuevo estado para la operación
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
                ]);
            });

            return redirect()->back()->with('success', 'Estado de la operación actualizado correctamente.');

        } catch (\Exception $e) {
            Log::error("CERTIFICADOS - Error al transicionar estado en operación {$id}: " . $e->getMessage());
            return redirect()->back()->with('error', 'Ocurrió un error al procesar el cambio de estado.');
        }
    }

    /**
     * 4. ASIGNA TIPOS: Vincular una tipología o subcategoría al evento/operación
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
                ]);
            });

            return redirect()->back()->with('success', 'Tipo de operación asignado correctamente.');

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
    // NUEVAS FUNCIONES PARA PROCESAMIENTO MASIVO E INDIVIDUAL
    // =========================================================================

    /**
     * 8. GENERACIÓN MASIVA (Procesamiento por Lotes / Bloque)
     */
    public function generarMasivo(Request $request)
    {
        // Aceptamos tanto 'bloque' como 'numero_bloque' para compatibilidad
        $bloque = $request->input('bloque') ?? $request->input('numero_bloque');

        if (!$bloque) {
            return back()->with('error', 'Debe seleccionar un lote (bloque) válido para procesar.');
        }

        try {
            DB::beginTransaction();

            $ahora = now();
            $totalOperacionesProcesadas = 0;

            // ChunkById procesa en bloques de 500 garantizando que SOLO ES EL LOTE SELECCIONADO
            CarSiaOperacion::where('numero_bloque', $bloque)
                ->chunkById(500, function ($operacionesChunk) use ($ahora, $bloque, &$totalOperacionesProcesadas) {
                    
                    $tercerosIds = $operacionesChunk->pluck('id_tercero')->toArray();
                    $operacionesMap = $operacionesChunk->keyBy('id_tercero');

                    // Traer facturas ESTRICTAMENTE del bloque seleccionado
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

                        $lineasAInsertar[] = [
                            'id_car_sia_operaciones' => $operacion->id,
                            'id_factura'             => $factura->id,
                            'id_car_sia_lineas'      => $factura->cuenta, // Usando la cuenta como llave foránea reparada
                            'numero_bloque'          => $bloque,
                            'observacion'            => "El asociado presenta una calificación $calificacion debido a un registro de $diasMora días de mora.",
                            'calificacion'           => $calificacion,
                            'fecha_venci'            => $factura->fecha_venci,
                            'id_car_sia_estados'     => 3, 
                            'dias_mora_automaticos'  => $diasMora,
                            'procesado_en'           => $ahora->format('Y-m-d H:i:s'),
                        ];
                    }

                    // Upsert Masivo
                    if (!empty($lineasAInsertar)) {
                        collect($lineasAInsertar)->chunk(1000)->each(function ($batch) {
                            CarSiaOperacionLinea::upsert(
                                $batch->toArray(),
                                ['id_car_sia_operaciones', 'id_factura'], 
                                [
                                    'id_car_sia_lineas', 
                                    'numero_bloque', 
                                    'observacion', 
                                    'calificacion', 
                                    'fecha_venci', 
                                    'id_car_sia_estados', 
                                    'dias_mora_automaticos', 
                                    'procesado_en'
                                ]
                            );
                        });
                    }

                    $totalOperacionesProcesadas += $operacionesChunk->count();
                });

            DB::commit();

            return back()->with('success', "Procesamiento masivo completado: Lote $bloque procesado exitosamente ($totalOperacionesProcesadas operaciones).");

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Error en procesamiento masivo: " . $e->getMessage() . " en la línea " . $e->getLine());
            return back()->with('error', 'Ocurrió un error en la base de datos: ' . $e->getMessage());
        }
    }

    /**
     * 9. GENERACIÓN INDIVIDUAL (Útil desde la vista Show)
     */
    public function generarIndividual($id)
    {
        try {
            $operacion = CarSiaOperacion::with('tercero')->findOrFail($id);
            
            $this->procesarLineasOperacion($operacion);

            $lineas = CarSiaOperacionLinea::where('id_car_sia_operaciones', $operacion->id)->get();

            $pdf = Pdf::loadView('certificados.pdf.certificado_aldia', compact('operacion', 'lineas'));
            return $pdf->stream("Certificado_{$operacion->numero_radicado}.pdf");

        } catch (\Exception $e) {
            Log::error("Error al generar certificado individual: " . $e->getMessage());
            return back()->with('error', 'Ocurrió un error al generar el certificado.');
        }
    }

    /**
     * 10. MOTOR DE REGLAS INTERNO (Para procesamiento 1 a 1)
     */
    private function procesarLineasOperacion($operacion)
    {
        $facturas = CarSiaApi::where('numero_bloque', $operacion->numero_bloque)
            ->where('tercero', $operacion->id_tercero)
            ->get();

        foreach ($facturas as $factura) {
            $diasMora = 0;

            if ($factura->fecha_venci) {
                $fechaVencimiento = Carbon::parse($factura->fecha_venci);
                $diferencia = now()->diffInDays($fechaVencimiento, false);
                $diasMora = $diferencia < 0 ? abs((int)$diferencia) : 0;
            }

            $calificacion = 'Bueno';
            if ($diasMora > 30 && $diasMora <= 60) {
                $calificacion = 'Regular';
            } elseif ($diasMora > 60) {
                $calificacion = 'Irregular';
            }

            $observacion = "El asociado presenta una calificación $calificacion debido a un registro de $diasMora días de mora.";

            CarSiaOperacionLinea::updateOrCreate(
                [
                    'id_car_sia_operaciones' => $operacion->id,
                    'id_factura'             => $factura->id,
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
                ]
            );
        }
    }
}