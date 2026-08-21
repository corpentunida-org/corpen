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

            return view('sia.config.index', compact(
                'configuraciones',
                'acciones',
                'estados',
                'tipos',
                'tiposAlerta'
            ));

        } catch (\Exception $e) {
            Log::error('SIA Config - Error al cargar el panel de configuración: ' . $e->getMessage());
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
            'parametros'                      => 'nullable|array', // Validamos que llegue como Array para el JSONB
            'frecuencia_recordatorio_dias'    => 'nullable|integer|min:1'
        ]);

        try {
            DB::transaction(function () use ($request) {
                CarSiaConfig::create([
                    'id_car_sia_acciones_vencimiento' => $request->id_car_sia_acciones_vencimiento,
                    'parametros'                      => $request->parametros, // Eloquent y el $casts lo convierten a JSONB
                    'frecuencia_recordatorio_dias'    => $request->frecuencia_recordatorio_dias,
                ]);
            });

            return redirect()->back()->with('success', 'Configuración guardada exitosamente.');

        } catch (\Exception $e) {
            Log::error('SIA Config - Error al guardar configuración JSONB: ' . $e->getMessage());
            return redirect()->back()->with('error', 'No se pudo guardar la configuración.');
        }
    }

    /**
     * 3. HABILITA/INHABILITA REGLAS: Alterna el estado de una acción de vencimiento
     */
    public function toggleAccionVencimiento(Request $request, $id)
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
            Log::error("SIA Config - Error al cambiar estado de acción {$id}: " . $e->getMessage());
            return redirect()->back()->with('error', 'Error al modificar el estado de la regla.');
        }
    }

    /**
     * 4. GESTIONA CATÁLOGOS: Crea un nuevo Tipo Base (Tipología)
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
            Log::error('SIA Config - Error al guardar Tipo: ' . $e->getMessage());
            return redirect()->back()->with('error', 'No se pudo registrar el tipo.');
        }
    }

    /**
     * 5. GESTIONA CATÁLOGOS: Crea un nuevo Estado de Operación
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
            Log::error('SIA Config - Error al guardar Estado: ' . $e->getMessage());
            return redirect()->back()->with('error', 'No se pudo guardar el estado.');
        }
    }

    /**
     * 6. GESTIONA CATÁLOGOS: Crea un nuevo Tipo de Alerta
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
            Log::error('SIA Config - Error al guardar Tipo de Alerta: ' . $e->getMessage());
            return redirect()->back()->with('error', 'No se pudo guardar el tipo de alerta.');
        }
    }
}
