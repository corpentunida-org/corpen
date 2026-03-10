<?php

namespace App\Http\Controllers\Inventario;

use App\Http\Controllers\Controller;
use App\Models\Inventario\InvActivo;
use App\Models\Inventario\InvMarca;
use App\Models\Inventario\InvSubgrupo;
use App\Models\Inventario\InvEstado;
use App\Models\Inventario\InvReferencia;
use App\Models\Maestras\MaeMunicipios;
use App\Models\Inventario\InvDetalleCompra;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ActivoController extends Controller
{
/**
     * Listado principal con filtros de búsqueda en tiempo real
     */
    public function index(Request $request)
    {
        $query = InvActivo::with(['marca', 'estado', 'usuarioAsignado', 'municipio', 'subgrupo', 'referencia']);
        
        // 1. Filtro por Búsqueda de Texto (Placa, Serial, Nombre Y RESPONSABLE)
        if($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('nombre', 'like', "%{$search}%")
                  ->orWhere('codigo_activo', 'like', "%{$search}%")
                  ->orWhere('serial', 'like', "%{$search}%")
                  // ESTA LÍNEA ES LA QUE PERMITE BUSCAR POR RESPONSABLE
                  ->orWhereHas('usuarioAsignado', function($qUser) use ($search) {
                      $qUser->where('name', 'like', "%{$search}%");
                  });
            });
        }

        // 2. Filtro por Estado
        if($request->filled('estado_id')) {
            $query->where('id_Estado', $request->estado_id);
        }

        // 3. Filtro por Marca 
        if($request->filled('marca_id')) {
            $query->whereHas('marca', function($q) use ($request) {
                $q->where('id', $request->marca_id);
            });
        }

        // 4. Filtro por Subgrupo (Categoría)
        if($request->filled('subgrupo_id')) {
            $query->whereHas('subgrupo', function($q) use ($request) {
                $q->where('id', $request->subgrupo_id);
            });
        }

        // Paginación 
        $activos = $query->latest()->paginate(15)->withQueryString();

        // Cargar catálogos
        $estados = InvEstado::orderBy('nombre')->get();
        $marcas = InvMarca::orderBy('nombre')->get();
        $subgrupos = InvSubgrupo::orderBy('nombre')->get();

        return view('inventario.activos.index', compact('activos', 'estados', 'marcas', 'subgrupos'));
    }
    /**
     * Formulario de edición: Carga el expediente completo
     */
    public function edit($id)
    {
        // 1. Cargar el activo
        $activo = InvActivo::with([
            'detalleCompra.compra', 
            'detalleCompra.referencia',
            'usuarioRegistro',
            'referencia.marca',
            'referencia.subgrupo'
        ])->findOrFail($id);
        
        // 2. LA SOLUCIÓN AL N+1 (Versión Segura)
        // Traemos las referencias con sus relaciones para evitar las 15,000 consultas,
        // pero dejamos que traiga todas las columnas para evitar errores de SQL.
        $referencias = InvReferencia::with(['marca', 'subgrupo', 'bodega'])
            ->orderBy('referencia')
            ->get(); 

        // 3. DIETA DE MEMORIA para el resto de catálogos
        $marcas = InvMarca::select('id', 'nombre')->orderBy('nombre')->get();
        $subgrupos = InvSubgrupo::select('id', 'nombre')->orderBy('nombre')->get();
        $estados = InvEstado::select('id', 'nombre')->get();
        $bodegas = DB::table('inv_bodegas')->select('id', 'nombre')->orderBy('nombre')->get(); 
        $municipios = MaeMunicipios::select('id', 'nombre')->orderBy('nombre')->get();
        $usuarios = User::select('id', 'name')->orderBy('name')->get();

        return view('inventario.activos.edit', compact(
            'activo', 'marcas', 'subgrupos', 'estados', 
            'municipios', 'referencias', 'usuarios', 'bodegas'
        ));
    }
    /**
     * Actualizar los datos del activo (Expediente Técnico)
     */
    public function update(Request $request, $id)
    {
        $activo = InvActivo::findOrFail($id);
        
        // Validación aumentada para cubrir los campos técnicos del modelo
        $request->validate([
            'nombre'            => 'required|string|max:255',
            'codigo_activo'     => 'required|string|unique:inv_activos,codigo_activo,'.$id,
            'invReferencias_id' => 'required|exists:inv_referencias,id',
            'id_Estado'         => 'required',
            'fecha_inicio_garantia' => 'nullable|date',
            'fecha_fin_garantia'    => 'nullable|date|after_or_equal:fecha_inicio_garantia',
            'vida_util_meses'       => 'nullable|integer|min:0',
        ]);

        // Actualización masiva de campos (nombre, serial, descripción, fechas, etc.)
        $activo->update($request->all());
        
        return redirect()->route('inventario.activos.show', $activo->id)
                         ->with('success', 'Expediente del activo actualizado con éxito');
    }

    /**
     * Hoja de Vida detallada para visualización e impresión
     */
    public function show($id)
    {
        $activo = InvActivo::with([
            'marca', 'estado', 'municipio', 'referencia', 
            'subgrupo', 'usuarioAsignado', 'mantenimientos', 'movimientos'
        ])->findOrFail($id);

        return view('inventario.activos.show', compact('activo'));
    }

    /**
     * Eliminar activo (Usa SoftDeletes definido en el modelo)
     */
    public function destroy($id)
    {
        $activo = InvActivo::findOrFail($id);
        $activo->delete();
        
        return redirect()->route('inventario.activos.index')
                         ->with('success', 'Activo enviado a la papelera');
    }

    /**
     * Mini-API AJAX para consultar el detalle de compra desde el Edit
     * Devuelve JSON para el modal minimalista
     */
    public function getDetalleCompraAjax($id)
    {
        // Cargamos relaciones para que el modal muestre nombre de referencia y factura maestra
        $detalle = InvDetalleCompra::with(['compra', 'referencia'])->find($id);

        if (!$detalle) {
            return response()->json([
                'success' => false, 
                'message' => 'El registro de compra no existe.'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data'    => $detalle
        ]);
    }

    /**
     * Muestra el panel de alertas (ej. Garantías por vencer)
     */
    public function alertas()
    {
        // 1. Equipos cuya garantía vence en los próximos 30 días
        $porVencer = InvActivo::whereNotNull('fecha_fin_garantia')
            ->whereDate('fecha_fin_garantia', '>=', now())
            ->whereDate('fecha_fin_garantia', '<=', now()->addDays(30))
            ->get();

        // 2. Equipos cuya garantía ya se venció
        $vencidos = InvActivo::whereNotNull('fecha_fin_garantia')
            ->whereDate('fecha_fin_garantia', '<', now())
            ->get();

        // Retornamos la vista pasándole estas dos listas
        return view('inventario.activos.alertas', compact('porVencer', 'vencidos'));
    }
}