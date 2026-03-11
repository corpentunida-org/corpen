<?php

namespace App\Http\Controllers\Inventario;

use App\Http\Controllers\Controller;
use App\Models\Inventario\InvActivo;
use App\Models\Inventario\InvMarca;
use App\Models\Inventario\InvSubgrupo;
use App\Models\Inventario\InvBodega;
use App\Models\Inventario\InvEstado;
use App\Models\Inventario\InvReferencia;
use App\Models\Maestras\MaeMunicipios;
use App\Models\Inventario\InvDetalleCompra;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;

class ActivoController extends Controller
{
    /**
     * Listado principal con filtros de búsqueda en tiempo real
     */
    public function index(Request $request)
    {
        // SE USA EL MOTOR DE FILTROS UNIFICADO PARA MANTENER CONSISTENCIA
        $query = $this->aplicarFiltros($request);

        // Paginación y Obtención de resultados filtrados
        $activos = $query->latest()->paginate(15)->withQueryString();

        // Catalogos
        $bodegas = InvBodega::orderBy('nombre')->get();
        $marcas = InvMarca::orderBy('nombre')->get();
        $subgrupos = InvSubgrupo::orderBy('nombre')->get();

        // Lógica de anidación de estados
        $estados = collect();
        if($request->filled('bodega_id')) {
            $estados = InvEstado::where('id_bodega', $request->bodega_id)
                                ->orderBy('nombre')
                                ->get();
        }

        return view('inventario.activos.index', compact('activos', 'estados', 'marcas', 'subgrupos', 'bodegas'));
    }

    /**
     * Generar Reporte PDF Profesional (CON LOS MISMOS FILTROS QUE EL INDEX)
     */
    public function generarReportePdf(Request $request)
    {
        // 1. USAMOS EL MISMO MOTOR DE FILTROS QUE EL INDEX PARA QUE EL PDF SEA EXACTO
        $query = $this->aplicarFiltros($request);

        // Obtenemos todos los resultados filtrados (sin paginar para el reporte completo)
        $activos = $query->latest()->get();
        
        // Calculamos totales para el encabezado del PDF
        $totalValor = $activos->sum(function($item) {
            return $item->detalleCompra->precio_unitario ?? 0;
        });

        // Generamos el PDF usando la vista profesional
        $pdf = Pdf::loadView('inventario.activos.pdf', [
            'activos' => $activos,
            'totalValor' => $totalValor,
            'fecha' => now()->format('d/m/Y h:i A'),
            'filtros' => $request->all()
        ])->setPaper('letter', 'landscape'); // Formato horizontal para más columnas

        return $pdf->download('Reporte_Inventario_'.now()->format('Ymd').'.pdf');
    }

    /**
     * MOTOR DE FILTROS UNIFICADO (MEJORA AGREGADA)
     * Esta función centraliza la lógica para que el INDEX y el PDF funcionen igual
     */
    private function aplicarFiltros(Request $request)
    {
        $query = InvActivo::with(['marca', 'estado', 'usuarioAsignado', 'municipio', 'subgrupo', 'referencia.bodega']);

        // 1. Filtro por Búsqueda de Texto
        if($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('nombre', 'like', "%{$search}%")
                ->orWhere('codigo_activo', 'like', "%{$search}%")
                ->orWhere('serial', 'like', "%{$search}%")
                ->orWhereHas('usuarioAsignado', function($qUser) use ($search) {
                    $qUser->where('name', 'like', "%{$search}%");
                });
            });
        }

        // 2. Filtro por Bodega
        if($request->filled('bodega_id')) {
            $query->whereHas('referencia', function($q) use ($request) {
                $q->where('id_InvBodegas', $request->bodega_id);
            });
        }

        // 3. Filtro por Estado (Solo si hay bodega seleccionada)
        if($request->filled('bodega_id') && $request->filled('estado_id')) {
            $query->where('id_Estado', $request->estado_id);
        }

        // 4. Filtros de Marca
        if($request->filled('marca_id')) {
            $query->whereHas('marca', function($q) use ($request) {
                $q->where('id', $request->marca_id);
            });
        }

        // 5. Filtro de Subgrupo
        if($request->filled('subgrupo_id')) {
            $query->whereHas('subgrupo', function($q) use ($request) {
                $q->where('id', $request->subgrupo_id);
            });
        }

        return $query;
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