<?php

namespace App\Http\Controllers\Archivo;

use App\Http\Controllers\Controller;
use App\Models\Archivo\GdoFuncion;
use App\Models\Archivo\GdoCargo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB; // <--- IMPORTANTE: Necesario para validar duplicados manualmente

class GdoFuncionController extends Controller
{
    /**
     * Muestra la lista unificada de funciones y el panel de asignación.
     */
    public function index()
    {
        // 1. Cargamos las funciones con el conteo de cargos (para saber si se pueden borrar)
        $funciones = GdoFuncion::withCount('cargos')->get();
        
        // 2. Cargamos los cargos y sus funciones relacionadas
        // Es CRUCIAL usar 'withPivot' para poder leer el estado (activo/inactivo) en la vista
        $cargos = GdoCargo::with(['funciones' => function($query) {
            $query->withPivot('estado'); 
        }])->orderBy('nombre_cargo', 'asc')->get();
        
        return view('archivo.funciones.index', compact('funciones', 'cargos'));
    }

    /**
     * Guarda una nueva función en el catálogo maestro.
     */
    public function store(Request $request)
    {
        $request->validate([
            'nombre' => 'required|max:100',
            'descripcion' => 'nullable'
        ]);

        GdoFuncion::create($request->all());
        
        return redirect()->back()->with('success', 'Función creada con éxito.');
    }

    /**
     * Actualiza los datos básicos de una función (Nombre/Descripción).
     */
    public function update(Request $request, GdoFuncion $funcion)
    {
        $request->validate([
            'nombre' => 'required|max:100',
            'descripcion' => 'nullable'
        ]);

        $funcion->update($request->all());
        
        return redirect()->back()->with('success', 'Función actualizada correctamente.');
    }

    /**
     * Elimina una función del catálogo maestro SOLO si no tiene vínculos.
     */
    public function destroy(GdoFuncion $funcion)
    {
        // Validación de Integridad: No borrar si está asignada (aunque esté inactiva)
        if ($funcion->cargos()->count() > 0) {
            return redirect()->back()->with('error', 'No se puede eliminar: Esta función está vinculada a uno o más cargos.');
        }

        $funcion->delete();
        
        return redirect()->back()->with('success', 'Función eliminada del sistema.');
    }

    /**
     * Crea la relación entre Cargo y Función (Tabla Pivot).
     * Validamos que NO exista previamente para evitar duplicados.
     */
    public function asignarCargo(Request $request)
    {
        $request->validate([
            'gdo_cargo_id' => 'required|exists:gdo_cargo,id',
            'gdo_funcion_id' => 'required|exists:gdo_funcion,id',
            'estado' => 'required|boolean',
        ]);

        // --- VALIDACIÓN DE DUPLICADOS ROBUSTA ---
        // Consultamos directamente la tabla intermedia para asegurar que no exista el par
        $existe = DB::table('gdo_funcion_cargo')
                    ->where('gdo_cargo_id', $request->gdo_cargo_id)
                    ->where('gdo_funcion_id', $request->gdo_funcion_id)
                    ->exists();

        if ($existe) {
            // Retornamos 'warning' para que Toastr muestre la alerta amarilla
            return redirect()->back()->with('warning', '¡Atención! Este cargo ya tiene asignada esa función. Verifique la lista inferior.');
        }
        // ----------------------------------------

        // Si no existe, creamos la relación usando attach
        $cargo = GdoCargo::find($request->gdo_cargo_id);
        
        $cargo->funciones()->attach($request->gdo_funcion_id, [
            'estado' => $request->estado
        ]);

        return redirect()->back()->with('success', 'Función vinculada correctamente.');
    }

    /**
     * Cambia el estado (Activo <-> Inactivo) de una vinculación existente.
     * Reemplaza a la eliminación física de la relación.
     */
    public function cambiarEstadoVinculo(Request $request, GdoCargo $cargo, GdoFuncion $funcion)
    {
        // Buscamos la relación específica en la tabla pivot usando Eloquent
        $relacion = $cargo->funciones()->where('gdo_funcion_id', $funcion->id)->first();

        if ($relacion) {
            // Invertimos el estado actual (true -> false, false -> true)
            $nuevoEstado = ! $relacion->pivot->estado;

            // Actualizamos solo el campo 'estado' en la tabla pivot
            $cargo->funciones()->updateExistingPivot($funcion->id, [
                'estado' => $nuevoEstado
            ]);

            $estadoTexto = $nuevoEstado ? 'ACTIVADA' : 'DESACTIVADA';
            return redirect()->back()->with('success', "Función $estadoTexto para el cargo seleccionado.");
        }

        return redirect()->back()->with('error', 'No se encontró la vinculación para modificar.');
    }
}