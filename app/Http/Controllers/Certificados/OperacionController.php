<?php

namespace App\Http\Controllers\Certificados;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Str; // Para generar el ID alfanumérico de las alertas
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log; // Importación clave para el registro de errores

// Importación de todos los modelos Core y Pivote (Fase 3 y 4)
use App\Models\Certificados\CarSiaOperacion;
use App\Models\Certificados\CarSiaOperacionLinea;
use App\Models\Certificados\CarSiaEstadoOperacion;
use App\Models\Certificados\CarSiaOperacionAlerta;
use App\Models\Certificados\CarSiaOperacionConfig;

class OperacionController extends Controller
{
    /**
     * 1. GESTIÓN MATRIZ: Listar el motor de operaciones
     */
    public function index(Request $request)
    {
        try {
            // Carga la matriz principal con sus relaciones básicas para la tabla
            $operaciones = CarSiaOperacion::with([
                'tercero',
                'factura',
                'estados.estado' // Trae el historial de estados y el nombre del estado
            ])
            ->orderBy('created_at', 'desc')
            ->paginate(20);

            return view('certificados.operaciones.index', compact('operaciones'));

        } catch (\Exception $e) {
            Log::error('CERTIFICADOS - Error cargando matriz de operaciones: ' . $e->getMessage());
            return back()->with('error', 'Ocurrió un error al cargar la matriz de operaciones.');
        }
    }

    /**
     * 2. DETALLE CRÉDITOS: Mostrar toda la trazabilidad de una operación
     */
    public function show($id)
    {
        try {
            // Carga la operación con TODO su árbol de dependencias (Líneas, Alertas, Tipos, Configuración)
            $operacion = CarSiaOperacion::with([
                'tercero',
                'lineas.lineaCredito',
                'lineas.estadoOperacion.estado',
                'alertas.tipoAlerta',
                'tiposEvento.tipo',
                'configuraciones.configuracionBase',
                'estados.estado'
            ])->findOrFail($id);

            return view('certificados.operaciones.show', compact('operacion'));

        } catch (\Exception $e) {
            Log::error("CERTIFICADOS - Error cargando el detalle de la operación {$id}: " . $e->getMessage());
            return redirect()->route('certificados.operaciones.index')
                             ->with('error', 'No se pudo cargar el detalle de la operación solicitada.');
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
                    'id'                      => (string) Str::uuid(), // Generamos el ID manual (VARCHAR 50)
                    'id_car_sia_tipos_alerta' => $request->id_car_sia_tipos_alerta,
                    'numero_bloque'           => $request->numero_bloque,
                    'id_car_sia_operaciones'  => $operacion->id,
                    'fecha_programada'        => $request->fecha_programada,
                    'procesado_en'            => null, // Nulo por defecto hasta que un CRON la procese
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

                // Usamos updateOrCreate para evitar duplicar la configuración de un mismo bloque
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
