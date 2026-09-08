<?php

namespace App\Http\Controllers\Certificados;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

// Importación de Modelos de Configuración y Catálogos Base
use App\Models\Certificados\CarSiaConfig;
use App\Models\Certificados\CarSiaAccionVencimiento;
use App\Models\Certificados\CarSiaEstado;
use App\Models\Certificados\CarSiaTipo;
use App\Models\Certificados\CarSiaTipoAlerta;
// Nuevos Modelos de Auditoría
use App\Models\Certificados\CarSiaOrigenEvento;
use App\Models\Certificados\CarSiaEventoAuditoria;

class ConfiguracionController extends Controller
{
    /**
     * 1. PANEL CENTRAL: Carga todas las configuraciones y catálogos en una sola vista
     * OPTIMIZACIÓN: Se implementa Caché para evitar 7 consultas a la base de datos en cada recarga.
     */
    public function index()
    {
        try {
            // Tiempo de vida del caché: 24 horas (86400 segundos)
            $ttl = 86400;

            $configuraciones = Cache::remember('sia_configuraciones', $ttl, function () {
                return CarSiaConfig::with('accionVencimiento')->orderBy('created_at', 'desc')->get();
            });

            $acciones = Cache::remember('sia_acciones_vencimiento', $ttl, function () {
                return CarSiaAccionVencimiento::orderBy('nombre')->get();
            });

            $estados = Cache::remember('sia_estados', $ttl, function () {
                return CarSiaEstado::orderBy('nombre')->get();
            });

            $tipos = Cache::remember('sia_tipos', $ttl, function () {
                return CarSiaTipo::orderBy('nombre')->get();
            });

            $tiposAlerta = Cache::remember('sia_tipos_alerta', $ttl, function () {
                return CarSiaTipoAlerta::orderBy('nombre')->get();
            });

            // Catálogos de Auditoría
            $origenesEvento = Cache::remember('sia_origenes_evento', $ttl, function () {
                return CarSiaOrigenEvento::orderBy('nombre')->get();
            });

            $eventosAuditoria = Cache::remember('sia_eventos_auditoria', $ttl, function () {
                return CarSiaEventoAuditoria::orderBy('nombre')->get();
            });

            return view('certificados.config.index', compact(
                'configuraciones',
                'acciones',
                'estados',
                'tipos',
                'tiposAlerta',
                'origenesEvento',
                'eventosAuditoria'
            ));

        } catch (\Exception $e) {
            Log::error('CERTIFICADOS Config - Error al cargar el panel de configuración: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Ocurrió un error al cargar los catálogos del sistema.');
        }
    }

    /**
     * 2. ADMINISTRA PARÁMETROS JSONB: Guarda una nueva regla de configuración Core
     */
    public function storeConfig(Request $request)
    {
        $request->validate([
            'id_car_sia_acciones_vencimiento' => 'required|exists:car_sia_acciones_vencimiento,id',
            'frecuencia_recordatorio_dias'    => 'nullable|integer|min:0',
            'estado_activo'                   => 'nullable', // Agregamos validación opcional

            // Validación de los campos que irán dentro del JSON
            'mora_dias_max'            => 'required|integer|min:0',
            'dias_gracia'              => 'required|integer|min:0',
            'clasificacion_mora'       => 'required|string|max:50',
            'observacion_fase'         => 'nullable|string|max:255'
        ]);

        try {
            // Construimos el arreglo con los booleanos (Checkbox envía 'on' si está marcado, nada si no)
            $parametrosArray = [
                'mora_dias_max'            => (int) $request->mora_dias_max,
                'dias_gracia'              => (int) $request->dias_gracia,
                'requiere_accion'          => $request->has('requiere_accion'),
                'clasificacion_mora'       => $request->clasificacion_mora,
                'incluir_historico_3_anos' => $request->has('incluir_historico_3_anos'),
                'notificacion_gerencia'    => $request->has('notificacion_gerencia'),
                'bloqueo_automatico'       => $request->has('bloqueo_automatico'),
                'observacion_fase'         => $request->observacion_fase ?? ''
            ];

            // Definimos el estado activo. Si en tu formulario tienes un checkbox name="estado_activo"
            // usamos has(). Si no existe el checkbox en tu HTML aún, puedes pasar simplemente true.
            $estadoActivo = $request->has('estado_activo');

            DB::transaction(function () use ($request, $parametrosArray, $estadoActivo) {
                CarSiaConfig::create([
                    'id_car_sia_acciones_vencimiento' => $request->id_car_sia_acciones_vencimiento,
                    'parametros'                      => $parametrosArray,
                    'frecuencia_recordatorio_dias'    => $request->frecuencia_recordatorio_dias,
                    'estado_activo'                   => $estadoActivo, // Pasamos el campo a la BD
                ]);
            });

            Cache::forget('sia_configuraciones');
            return redirect()->back()->with('success', 'Regla matemática y configuración guardada exitosamente.');

        } catch (\Exception $e) {
            Log::error('CERTIFICADOS Config - Error al guardar configuración JSONB paramétrica: ' . $e->getMessage());
            return redirect()->back()->with('error', 'No se pudo guardar la configuración paramétrica.');
        }
    }

    /**
     * 3. HABILITA/INHABILITA REGLAS: Alterna el estado de una acción de vencimiento
     */
    public function toggleAccionVencimiento(Request $request, int $id)
    {
        try {
            DB::transaction(function () use ($id) {
                $accion = CarSiaAccionVencimiento::findOrFail($id);
                $accion->estado = !$accion->estado;
                $accion->save();
            });

            Cache::forget('sia_acciones_vencimiento');
            Cache::forget('sia_configuraciones'); // Por si se muestran relaciones afectadas

            return redirect()->back()->with('success', 'Estado de la acción actualizado.');
        } catch (\Exception $e) {
            Log::error("CERTIFICADOS Config - Error al cambiar estado de acción {$id}: " . $e->getMessage());
            return redirect()->back()->with('error', 'Error al modificar el estado de la regla.');
        }
    }
    /**
     * 3.1. HABILITA/INHABILITA CONFIGURACIÓN CORE: Alterna el estado activo de una regla completa
     */
    public function toggleEstadoConfig(Request $request, int $id)
    {
        try {
            DB::transaction(function () use ($id) {
                // Buscamos la configuración específica
                $config = CarSiaConfig::findOrFail($id);
                // Invertimos su estado actual (si es true pasa a false, y viceversa)
                $config->estado_activo = !$config->estado_activo;
                $config->save();
            });

            // Limpiamos el caché para que el cambio se refleje de inmediato en la tabla
            Cache::forget('sia_configuraciones');

            return redirect()->back()->with('success', 'El estado de la configuración (regla) ha sido actualizado.');
        } catch (\Exception $e) {
            Log::error("CERTIFICADOS Config - Error al cambiar estado de la configuración {$id}: " . $e->getMessage());
            return redirect()->back()->with('error', 'Ocurrió un error al intentar modificar el estado de la regla.');
        }
    }
    
    /**
     * 4. GESTIONA CATÁLOGOS: Crea una nueva Acción de Vencimiento
     */
    public function storeAccionVencimiento(Request $request)
    {
        $request->validate(['nombre' => 'required|string|max:100']);
        try {
            CarSiaAccionVencimiento::create(['nombre' => $request->nombre, 'estado' => true]);
            Cache::forget('sia_acciones_vencimiento');
            return redirect()->back()->with('success', 'Acción de vencimiento creada correctamente.');
        } catch (\Exception $e) {
            Log::error('CERTIFICADOS Config - Error al guardar Acción de Vencimiento: ' . $e->getMessage());
            return redirect()->back()->with('error', 'No se pudo registrar la acción.');
        }
    }

    /**
     * 5. GESTIONA CATÁLOGOS: Crea un nuevo Tipo Base (Tipología)
     */
    public function storeTipo(Request $request)
    {
        $request->validate([
            'nombre'              => 'required|string|max:100',
            'estructura_radicado' => 'required|string|max:50',
            'estado'              => 'boolean'
        ]);

        try {
            CarSiaTipo::create([
                'nombre'              => $request->nombre,
                'estructura_radicado' => $request->estructura_radicado,
                'estado'              => $request->estado ?? true,
            ]);
            Cache::forget('sia_tipos');
            return redirect()->back()->with('success', 'Nuevo tipo agregado al catálogo.');
        } catch (\Exception $e) {
            Log::error('CERTIFICADOS Config - Error al guardar Tipo: ' . $e->getMessage());
            return redirect()->back()->with('error', 'No se pudo registrar el tipo.');
        }
    }

    /**
     * 6. GESTIONA CATÁLOGOS: Crea un nuevo Estado de Operación
     */
    public function storeEstado(Request $request)
    {
        $request->validate(['nombre' => 'required|string|max:100|unique:car_sia_estados,nombre']);
        try {
            CarSiaEstado::create(['nombre' => $request->nombre]);
            Cache::forget('sia_estados');
            return redirect()->back()->with('success', 'Estado registrado correctamente.');
        } catch (\Exception $e) {
            Log::error('CERTIFICADOS Config - Error al guardar Estado: ' . $e->getMessage());
            return redirect()->back()->with('error', 'No se pudo guardar el estado.');
        }
    }

    /**
     * 7. GESTIONA CATÁLOGOS: Crea un nuevo Tipo de Alerta
     */
    public function storeTipoAlerta(Request $request)
    {
        $request->validate(['nombre' => 'required|string|max:255|unique:car_sia_tipos_alerta,nombre']);
        try {
            CarSiaTipoAlerta::create(['nombre' => $request->nombre]);
            Cache::forget('sia_tipos_alerta');
            return redirect()->back()->with('success', 'Tipo de alerta registrado en el catálogo.');
        } catch (\Exception $e) {
            Log::error('CERTIFICADOS Config - Error al guardar Tipo de Alerta: ' . $e->getMessage());
            return redirect()->back()->with('error', 'No se pudo guardar el tipo de alerta.');
        }
    }

    /**
     * 8. AUDITORÍA: Crea un nuevo Origen de Evento (ej. Web, Cron, API)
     */
    public function storeOrigenEvento(Request $request)
    {
        $request->validate(['nombre' => 'required|string|max:100|unique:car_sia_origenes_evento,nombre']);
        try {
            CarSiaOrigenEvento::create(['nombre' => $request->nombre]);
            Cache::forget('sia_origenes_evento');
            return redirect()->back()->with('success', 'Origen de evento registrado en el catálogo de auditoría.');
        } catch (\Exception $e) {
            Log::error('CERTIFICADOS Config - Error al guardar Origen de Evento: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Error SQL: ' . $e->getMessage());
        }
    }

    /**
     * 9. AUDITORÍA: Crea un nuevo Evento de Auditoría
     */
    public function storeEventoAuditoria(Request $request)
    {
        $request->validate(['nombre' => 'required|string|max:100|unique:car_sia_eventos_auditoria,nombre']);
        try {
            CarSiaEventoAuditoria::create(['nombre' => $request->nombre]);
            Cache::forget('sia_eventos_auditoria');
            return redirect()->back()->with('success', 'Evento de auditoría registrado correctamente.');
        } catch (\Exception $e) {
            Log::error('CERTIFICADOS Config - Error al guardar Evento de Auditoría: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Error SQL: ' . $e->getMessage());
        }
    }
}
