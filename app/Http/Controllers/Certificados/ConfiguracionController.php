<?php

namespace App\Http\Controllers\Certificados;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

// Importación de Modelos de Configuración y Catálogos Base
use App\Models\Certificados\CarSiaConfig;
use App\Models\Certificados\CarSiaAccionVencimiento;
use App\Models\Certificados\CarSiaEstado;
use App\Models\Certificados\CarSiaTipo;
use App\Models\Certificados\CarSiaTipoAlerta;

class ConfiguracionController extends Controller
{
    /**
     * 1. PANEL CENTRAL: Carga todas las configuraciones y catálogos en una sola vista
     */
    public function index()
    {
        try {
            $configuraciones = CarSiaConfig::with('accionVencimiento')->orderBy('created_at', 'desc')->get();
            $acciones        = CarSiaAccionVencimiento::orderBy('nombre')->get();
            $estados         = CarSiaEstado::orderBy('nombre')->get();
            $tipos           = CarSiaTipo::orderBy('nombre')->get();
            $tiposAlerta     = CarSiaTipoAlerta::orderBy('nombre')->get();

            return view('certificados.config.index', compact(
                'configuraciones',
                'acciones',
                'estados',
                'tipos',
                'tiposAlerta'
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
        // 1. Cambiamos la validación de 'array' a 'json'
        $request->validate([
            'id_car_sia_acciones_vencimiento' => 'required|exists:car_sia_acciones_vencimiento,id',
            'parametros'                      => 'nullable|json', 
            'frecuencia_recordatorio_dias'    => 'nullable|integer|min:1'
        ], [
            'parametros.json' => 'El campo de parámetros debe ser un JSON válido. Revisa las comillas y comas.'
        ]);

        try {
            // 2. Decodificamos el texto JSON a un Array de PHP antes de guardarlo
            $parametrosArray = $request->parametros ? json_decode($request->parametros, true) : null;

            DB::transaction(function () use ($request, $parametrosArray) {
                CarSiaConfig::create([
                    'id_car_sia_acciones_vencimiento' => $request->id_car_sia_acciones_vencimiento,
                    'parametros'                      => $parametrosArray, // Pasamos el array decodificado
                    'frecuencia_recordatorio_dias'    => $request->frecuencia_recordatorio_dias,
                ]);
            });

            return redirect()->back()->with('success', 'Configuración guardada exitosamente.');

        } catch (\Exception $e) {
            Log::error('CERTIFICADOS Config - Error al guardar configuración JSONB: ' . $e->getMessage());
            return redirect()->back()->with('error', 'No se pudo guardar la configuración.');
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
                // Alterna el booleano actual (Si es true pasa a false y viceversa)
                $accion->estado = !$accion->estado;
                $accion->save();
            });

            return redirect()->back()->with('success', 'Estado de la acción actualizado.');

        } catch (\Exception $e) {
            Log::error("CERTIFICADOS Config - Error al cambiar estado de acción {$id}: " . $e->getMessage());
            return redirect()->back()->with('error', 'Error al modificar el estado de la regla.');
        }
    }

    /**
     * 4. GESTIONA CATÁLOGOS: Crea una nueva Acción de Vencimiento
     */
    public function storeAccionVencimiento(Request $request)
    {
        $request->validate([
            'nombre' => 'required|string|max:100'
        ]);

        try {
            CarSiaAccionVencimiento::create([
                'nombre' => $request->nombre,
                'estado' => true // Por defecto nacen activas
            ]);

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
        $request->validate([
            'nombre' => 'required|string|max:100|unique:car_sia_estados,nombre'
        ]);

        try {
            CarSiaEstado::create([
                'nombre' => $request->nombre,
            ]);

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
        $request->validate([
            'nombre' => 'required|string|max:255|unique:car_sia_tipos_alerta,nombre'
        ]);

        try {
            CarSiaTipoAlerta::create([
                'nombre' => $request->nombre,
            ]);

            return redirect()->back()->with('success', 'Tipo de alerta registrado en el catálogo.');

        } catch (\Exception $e) {
            Log::error('CERTIFICADOS Config - Error al guardar Tipo de Alerta: ' . $e->getMessage());
            return redirect()->back()->with('error', 'No se pudo guardar el tipo de alerta.');
        }
    }
}