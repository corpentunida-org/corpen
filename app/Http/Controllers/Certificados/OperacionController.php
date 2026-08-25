<?php

namespace App\Http\Controllers\Certificados;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

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
            // Evita hacer GROUP BY y MAX en 30,000 registros en cada F5
            // =================================================================
            $bloquesDisponibles = Cache::remember('sia_bloques_disponibles', 300, function () {
                return CarSiaOperacion::whereNotNull('numero_bloque')
                    ->select('numero_bloque', DB::raw('MAX(created_at) as fecha_ejecucion'))
                    ->groupBy('numero_bloque')
                    ->orderBy('fecha_ejecucion', 'desc')
                    ->get();
            });

            $bloqueActivo = $request->input('bloque', $bloquesDisponibles->first()->numero_bloque ?? null);

            // =================================================================
            // OPTIMIZACIÓN 2: KPIs CON QUERIES NATIVAS (whereExists)
            // Reemplazamos whereHas por whereExists para evitar colapso de RAM
            // =================================================================
            $kpi = [
                'total'      => 0,
                'procesados' => 0,
                'pendientes' => 0,
            ];

            if ($bloqueActivo) {
                // Conteo total súper rápido
                $kpi['total'] = CarSiaOperacion::where('numero_bloque', $bloqueActivo)->count();

                // Conteo de procesados optimizado en SQL puro (evita N+1 y Model Hydration)
                $kpi['procesados'] = CarSiaOperacion::where('numero_bloque', $bloqueActivo)
                    ->whereExists(function ($query) {
                        $query->select(DB::raw(1))
                              // 👇 AQUÍ ESTABA EL ERROR: Nombre exacto de la tabla corregido
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
            // Al estar paginado a 20, Eloquent solo hidrata 20 filas, lo cual es instantáneo
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
            Log::error('CERTIFICADOS - Error matriz: ' . $e->getMessage());
            return back()->with('error', 'Error interno al cargar: ' . $e->getMessage());
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
                'lineas.lineaCredito',
                'lineas.estadoOperacion.estado',
                'lineas.factura'
            ])->findOrFail($id);

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

            return view('certificados.operaciones.show', compact(
                'operacion', 'historialEstados', 'historialTipos', 'historialAlertas', 'estados', 'tipos', 'tiposAlerta'
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
}
