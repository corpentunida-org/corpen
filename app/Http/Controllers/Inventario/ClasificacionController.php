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
    public function index()
    {
        // 1. Principal: Listamos los Subgrupos con toda su jerarquía (Paginado)
        $subgrupos = InvSubgrupo::with(['grupo', 'linea', 'tipo'])
                        ->latest()
                        ->paginate(10);
        
        // 2. Selectores: Listas simples para los <select> de los formularios
        $grupos = InvGrupo::orderBy('nombre')->get();
        $lineas = InvLinea::orderBy('nombre')->get();
        $tipos  = InvTipo::orderBy('nombre')->get();

        // 3. Auditoría: Listas COMPLETAS con relaciones para las tablas de la derecha
        //    (Esto es lo que faltaba para que funcionen los tabs de auditoría)
        $tipos_list  = InvTipo::withCount('subgrupos')->orderBy('nombre')->get();
        $lineas_list = InvLinea::with('tipo')->orderBy('nombre')->get();
        $grupos_list = InvGrupo::with('linea')->orderBy('nombre')->get();

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
        $request->validate([
            'nombre'       => 'required|string|max:255',
            'id_InvGrupos' => 'required|exists:inv_grupos,id',
            'id_InvLineas' => 'required|exists:inv_lineas,id',
            'id_InvTipos'  => 'required|exists:inv_tipos,id',
        ]);

        $subgrupo = InvSubgrupo::findOrFail($id);
        $subgrupo->update($request->all());

        return back()->with('success', 'Clasificación actualizada correctamente.');
    }

    /**
     * Elimina un SUBGRUPO.
     */
    public function destroy($id)
    {
        InvSubgrupo::destroy($id);
        return back()->with('success', 'Registro eliminado.');
    }

    // =========================================================================
    // MÉTODOS PARA CREAR CATÁLOGOS BASE (CON JERARQUÍA)
    // =========================================================================

    // --- GRUPOS ---
    public function storeGrupo(Request $request)
    {
        $request->validate([
            'nombre'       => 'required|string|max:255|unique:inv_grupos,nombre',
            'id_InvLineas' => 'required|exists:inv_lineas,id'
        ]);
        InvGrupo::create($request->all());
        return back()->with('success', 'Grupo creado y vinculado a la Línea.');
    }

    public function updateGrupo(Request $request, $id)
    {
        $grupo = InvGrupo::findOrFail($id);
        $grupo->update($request->all()); // Asegúrate de validar si cambias nombre o padre
        return back()->with('success', 'Grupo actualizado.');
    }

    // --- LÍNEAS ---
    public function storeLinea(Request $request)
    {
        $request->validate([
            'nombre'      => 'required|string|max:255|unique:inv_lineas,nombre',
            'id_InvTipos' => 'required|exists:inv_tipos,id'
        ]);
        InvLinea::create($request->all());
        return back()->with('success', 'Línea creada y vinculada al Tipo.');
    }

    public function updateLinea(Request $request, $id)
    {
        $linea = InvLinea::findOrFail($id);
        $linea->update($request->all());
        return back()->with('success', 'Línea actualizada.');
    }

    // --- TIPOS ---
    public function storeTipo(Request $request)
    {
        $request->validate(['nombre' => 'required|string|max:255|unique:inv_tipos,nombre']);
        InvTipo::create($request->all());
        return back()->with('success', 'Tipo creado.');
    }

    public function updateTipo(Request $request, $id)
    {
        $tipo = InvTipo::findOrFail($id);
        $tipo->update($request->all());
        return back()->with('success', 'Tipo actualizado.');
    }

    // --- ELIMINAR PARÁMETROS ---
    public function destroyParametro($id, $tipo)
    {
        try {
            if ($tipo == 'grupo') InvGrupo::destroy($id);
            if ($tipo == 'linea') InvLinea::destroy($id);
            if ($tipo == 'tipo')  InvTipo::destroy($id);
            return back()->with('success', 'Parámetro eliminado.');
        } catch (\Exception $e) {
            return back()->with('error', 'No se puede eliminar: Está en uso por registros dependientes.');
        }
    }
    
    // --- MÉTODOS AJAX (Opcionales, para filtros dinámicos si los usas) ---
    public function getLineasByGrupo($grupo_id) {
        // Nota: En tu lógica inversa (Grupo -> Linea), esto sería al revés.
        // Pero asumiendo el flujo de creación: Seleccionas Tipo -> Cargas Líneas
        return InvLinea::where('id_InvTipos', $grupo_id)->get(); 
    }

    public function getSubgruposByLinea($linea_id) {
        return InvGrupo::where('id_InvLineas', $linea_id)->get();
    }
}