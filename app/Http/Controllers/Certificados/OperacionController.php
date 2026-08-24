<?php

namespace App\Http\Controllers\Certificados;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

use App\Models\Certificados\CarSiaOperacion;
use App\Models\Certificados\CarSiaOperacionLinea;
use App\Models\Certificados\CarSiaEstadoOperacion;
use App\Models\Certificados\CarSiaOperacionAlerta;
use App\Models\Certificados\CarSiaOperacionConfig;

class OperacionController extends Controller
{
    /**
     * 1. GESTIÓN MATRIZ: Listar el motor de operaciones aislado por LOTES
     */
    public function index(Request $request)
    {
        try {
            // 1. Extraer Bloques con su fecha de ejecución
            $bloquesDisponibles = CarSiaOperacion::whereNotNull('numero_bloque')
                ->select('numero_bloque', DB::raw('MAX(created_at) as fecha_ejecucion'))
                ->groupBy('numero_bloque')
                ->orderBy('fecha_ejecucion', 'desc')
                ->get();

            // 2. Definir el Lote Activo
            $bloqueActivo = $request->input('bloque', $bloquesDisponibles->first()->numero_bloque ?? null);

            // =================================================================
            // 3. CÁLCULO DE KPIs (NUEVO)
            // =================================================================
            $kpi = [
                'total'      => 0,
                'procesados' => 0,
                'pendientes' => 0,
            ];

            if ($bloqueActivo) {
                $kpi['total'] = CarSiaOperacion::where('numero_bloque', $bloqueActivo)->count();

                // Buscamos cuántos tienen un estado de éxito (ajusta 'Procesado' o 'Aprobado' según los nombres de tu tabla car_sia_estados)
                $kpi['procesados'] = CarSiaOperacion::where('numero_bloque', $bloqueActivo)
                    ->whereHas('estados.estado', function($q) {
                        $q->where('nombre', 'LIKE', '%Procesado%')
                          ->orWhere('nombre', 'LIKE', '%Aprobado%')
                          ->orWhere('nombre', 'LIKE', '%Completado%');
                    })->count();

                $kpi['pendientes'] = $kpi['total'] - $kpi['procesados'];
            }
            // =================================================================

            $query = CarSiaOperacion::with([
                'tercero',
                'estados.estado'
            ]);

            // 4. AISLAMIENTO TOTAL
            if ($bloqueActivo) {
                $query->where('numero_bloque', $bloqueActivo);
            } else {
                $query->where('id', 0);
            }

            // --- APLICACIÓN DE FILTROS SECUNDARIOS ---
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

            // Extraer Años
            $aniosDisponibles = CarSiaOperacion::whereNotNull('created_at')
                ->selectRaw('YEAR(created_at) as anio')
                ->groupBy('anio')
                ->orderBy('anio', 'desc')
                ->pluck('anio');

            return view('certificados.operaciones.index', compact('operaciones', 'aniosDisponibles', 'bloquesDisponibles', 'bloqueActivo', 'kpi'));

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
                'lineas.estadoOperacion',
                'estados.estado'
            ])->findOrFail($id);

            return view('certificados.operaciones.show', compact('operacion'));

        } catch (\Exception $e) {
            dd('🚨 ERROR AL ABRIR EL EXPEDIENTE (SHOW):', $e->getMessage());
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

                DB::table('car_sia_tipos_evento')->insert([
                    'id_car_sia_operaciones' => $operacion->id,
                    'id_car_sia_tipos'       => $request->id_car_sia_tipos,
                    'numero_bloque'          => $request->numero_bloque,
                    'created_at'             => now(),
                    'updated_at'             => now(),
                ]);
            });

            return redirect()->back()->with('success', 'Tipo de evento asignado correctamente.');

        } catch (\Exception $e) {
            Log::error("SIA - Error al asignar tipo en operación {$id}: " . $e->getMessage());
            return redirect()->back()->with('error', 'Ocurrió un error al asignar el tipo de evento.');
        }
    }

    /**
     * 5. PROGRAMA ALERTAS: Crear una nueva alerta para la operación
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
     * 6. ACTIVA NOTIFICACIONES: Configurar reglas de notificación para el bloque/operación
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
