<?php

namespace App\Http\Controllers\Inventario;

use App\Http\Controllers\Controller;
use App\Models\Inventario\InvSubgrupo;
use App\Models\Inventario\InvGrupo;
use App\Models\Inventario\InvLinea;
use App\Models\Inventario\InvTipo;
use Illuminate\Http\Request;

class ClasificacionController extends Controller
{
    /**
     * Muestra la vista principal.
     */
    public function index(Request $request)
    {
        // 1. Principal: Listamos los Subgrupos con toda su jerarquía (Paginado)
        $subgruposQuery = InvSubgrupo::with(['grupo', 'linea', 'tipo']);
        
        // Aplicar filtros para subgrupos
        if ($request->has('search_sub') && !empty($request->search_sub)) {
            $subgruposQuery->where('nombre', 'like', '%' . $request->search_sub . '%');
        }
        
        if ($request->has('filter_tipo_sub') && !empty($request->filter_tipo_sub)) {
            $subgruposQuery->where('id_InvTipos', $request->filter_tipo_sub);
        }
        
        if ($request->has('filter_linea_sub') && !empty($request->filter_linea_sub)) {
            $subgruposQuery->where('id_InvLineas', $request->filter_linea_sub);
        }
        
        if ($request->has('filter_grupo_sub') && !empty($request->filter_grupo_sub)) {
            $subgruposQuery->where('id_InvGrupos', $request->filter_grupo_sub);
        }
        
        $subgrupos = $subgruposQuery->latest()->paginate(10);
        
        // 2. Selectores: Listas simples para los <select> de los formularios
        $grupos = InvGrupo::orderBy('nombre')->get();
        $lineas = InvLinea::orderBy('nombre')->get();
        $tipos  = InvTipo::orderBy('nombre')->get();

        // 3. Auditoría: Listas COMPLETAS con "withCount" para saber si tienen hijos y bloquear borrado
        
        // Tipo: Contar líneas (hijos directos) y subgrupos (nietos/finales)
        $tiposQuery = InvTipo::withCount(['lineas', 'subgrupos']);
        
        // Aplicar filtros para tipos
        if ($request->has('search_tipo') && !empty($request->search_tipo)) {
            $tiposQuery->where('nombre', 'like', '%' . $request->search_tipo . '%');
        }
        
        $tipos_list = $tiposQuery->orderBy('nombre')->get();

        // Línea: Contar grupos (hijos directos) y subgrupos
        $lineasQuery = InvLinea::with('tipo')->withCount(['grupos', 'subgrupos']);
        
        // Aplicar filtros para líneas
        if ($request->has('search_linea') && !empty($request->search_linea)) {
            $lineasQuery->where('nombre', 'like', '%' . $request->search_linea . '%');
        }
        
        if ($request->has('filter_tipo_linea') && !empty($request->filter_tipo_linea)) {
            $lineasQuery->where('id_InvTipos', $request->filter_tipo_linea);
        }
        
        $lineas_list = $lineasQuery->orderBy('nombre')->get();

        // Grupo: Contar subgrupos (hijos directos)
        $gruposQuery = InvGrupo::with('linea')->withCount('subgrupos');
        
        // Aplicar filtros para grupos
        if ($request->has('search_grupo') && !empty($request->search_grupo)) {
            $gruposQuery->where('nombre', 'like', '%' . $request->search_grupo . '%');
        }
        
        if ($request->has('filter_linea_grupo') && !empty($request->filter_linea_grupo)) {
            $gruposQuery->where('id_InvLineas', $request->filter_linea_grupo);
        }
        
        $grupos_list = $gruposQuery->orderBy('nombre')->get();

        return view('inventario.catalogos.clasificacion.index', compact(
            'subgrupos', 
            'grupos', 'lineas', 'tipos',
            'tipos_list', 'lineas_list', 'grupos_list'
        ));
    }

    /**
     * Guarda el SUBGRUPO (La vinculación final).
     */
    public function store(Request $request)
    {
        $request->validate([
            'nombre'       => 'required|string|max:255',
            'descripcion'  => 'nullable|string',
            'id_InvGrupos' => 'required|exists:inv_grupos,id',
            'id_InvLineas' => 'required|exists:inv_lineas,id',
            'id_InvTipos'  => 'required|exists:inv_tipos,id',
        ]);

        InvSubgrupo::create($request->all());
        return back()->with('success', 'Clasificación creada correctamente.');
    }

    /**
     * Actualiza el SUBGRUPO (Edición).
     */
    public function update(Request $request, $id)
    {
        // --- ACTIVAR LOG DE CONSULTAS (TEMPORAL) ---
        // ------------------------------------------

        $request->validate([
            'nombre'       => 'required|string|max:255',
            'descripcion'  => 'nullable|string',
            'id_InvGrupos' => 'required|exists:inv_grupos,id',
            'id_InvLineas' => 'required|exists:inv_lineas,id',
            'id_InvTipos'  => 'required|exists:inv_tipos,id',
        ]);

        $subgrupo = InvSubgrupo::findOrFail($id);
        $subgrupo->nombre = $request->input('nombre');
        $subgrupo->descripcion = $request->input('descripcion');
        $subgrupo->id_InvGrupos = $request->input('id_InvGrupos');
        $subgrupo->id_InvLineas = $request->input('id_InvLineas');
        $subgrupo->id_InvTipos = $request->input('id_InvTipos');
        
        $subgrupo->save();

        // --- MOSTRAR LAS CONSULTAS EJECUTADAS ---
        
        // ----------------------------------------

        return back()->with('success', 'Clasificación actualizada correctamente.');
    }

    /**
     * Elimina un SUBGRUPO.
     */
    public function destroy($id)
    {
        try {
            // Nota: Aquí podrías validar si existe Inventario Físico antes de borrar
            // if(InvItem::where('id_InvSubgrupos', $id)->exists()) { ... }
            
            InvSubgrupo::destroy($id);
            return back()->with('success', 'Registro eliminado.');
        } catch (\Exception $e) {
            return back()->with('error', 'No se puede eliminar: El subgrupo está en uso en el inventario activo.');
        }
    }

    // =========================================================================
    // MÉTODOS PARA CREAR CATÁLOGOS BASE (CON JERARQUÍA)
    // =========================================================================

    // --- GRUPOS ---
    public function storeGrupo(Request $request)
    {
        $request->validate([
            'nombre'       => 'required|string|max:255|unique:inv_grupos,nombre',
            'descripcion'  => 'nullable|string',
            'id_InvLineas' => 'required|exists:inv_lineas,id'
        ]);
        InvGrupo::create($request->all());
        return back()->with('success', 'Grupo creado y vinculado a la Línea.');
    }

    public function updateGrupo(Request $request, $id)
    {
        $request->validate([
            'nombre'      => 'required|string|max:255',
            'descripcion' => 'nullable|string',
        ]);
        
        $grupo = InvGrupo::findOrFail($id);
        $grupo->update($request->all()); 
        return back()->with('success', 'Grupo actualizado.');
    }

    // --- LÍNEAS ---
    public function storeLinea(Request $request)
    {
        $request->validate([
            'nombre'      => 'required|string|max:255|unique:inv_lineas,nombre',
            'descripcion' => 'nullable|string',
            'id_InvTipos' => 'required|exists:inv_tipos,id'
        ]);
        InvLinea::create($request->all());
        return back()->with('success', 'Línea creada y vinculada al Tipo.');
    }

    public function updateLinea(Request $request, $id)
    {
        $request->validate([
            'nombre'      => 'required|string|max:255',
            'descripcion' => 'nullable|string',
        ]);
        
        $linea = InvLinea::findOrFail($id);
        $linea->update($request->all());
        return back()->with('success', 'Línea actualizada.');
    }

    // --- TIPOS ---
    public function storeTipo(Request $request)
    {
        $request->validate([
            'nombre'      => 'required|string|max:255|unique:inv_tipos,nombre',
            'descripcion' => 'nullable|string'
        ]);
        InvTipo::create($request->all());
        return back()->with('success', 'Tipo creado.');
    }

    public function updateTipo(Request $request, $id)
    {
        $request->validate([
            'nombre'      => 'required|string|max:255',
            'descripcion' => 'nullable|string',
        ]);
        
        $tipo = InvTipo::findOrFail($id);
        $tipo->update($request->all());
        return back()->with('success', 'Tipo actualizado.');
    }

    // --- ELIMINAR PARÁMETROS CON VALIDACIÓN ---
    public function destroyParametro($id, $tipo)
    {
        try {
            if ($tipo == 'grupo') {
                $grupo = InvGrupo::findOrFail($id);
                if($grupo->subgrupos()->count() > 0) {
                    return back()->with('error', 'No se puede eliminar: El Grupo tiene Subgrupos vinculados.');
                }
                $grupo->delete();
            }

            if ($tipo == 'linea') {
                $linea = InvLinea::findOrFail($id);
                if($linea->grupos()->count() > 0) {
                    return back()->with('error', 'No se puede eliminar: La Línea tiene Grupos vinculados.');
                }
                $linea->delete();
            }

            if ($tipo == 'tipo') {
                $objTipo = InvTipo::findOrFail($id);
                if($objTipo->lineas()->count() > 0) {
                    return back()->with('error', 'No se puede eliminar: El Tipo tiene Líneas vinculadas.');
                }
                $objTipo->delete();
            }

            return back()->with('success', 'Parámetro eliminado correctamente.');

        } catch (\Exception $e) {
            return back()->with('error', 'Error crítico: No se puede eliminar por integridad referencial base de datos.');
        }
    }
    
    // --- MÉTODOS AJAX ---
    public function getLineasByGrupo($grupo_id) {
        return InvLinea::where('id_InvTipos', $grupo_id)->get(); 
    }

    public function getSubgruposByLinea($linea_id) {
        return InvGrupo::where('id_InvLineas', $linea_id)->get();
    }
}