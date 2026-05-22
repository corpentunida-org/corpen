<?php

namespace App\Http\Controllers\Inventario;

use App\Http\Controllers\Controller;
use App\Models\Inventario\InvActivo;
use App\Models\Inventario\InvMovimiento;
use Illuminate\Http\Request;

class TableroInventarioController extends Controller
{
    public function index(Request $request)
    {
        // 1. Total de Activos
        $totalActivos = InvActivo::count();

        // 2. Consultamos agrupando e incluyendo la relación 'estado'
        $activosAgrupados = InvActivo::selectRaw('id_Estado, count(*) as total')
                                     ->with('estado')
                                     ->groupBy('id_Estado')
                                     ->get();

        $labelsGrafica = [];
        $dataGrafica = [];
        $activosPorEstado = [];

        foreach ($activosAgrupados as $item) {
            $labelsGrafica[] = $item->estado ? $item->estado->nombre : 'Estado ' . $item->id_Estado;
            $dataGrafica[]   = $item->total;
            $activosPorEstado[$item->id_Estado] = $item->total;
        }

        // --- EXTRACCIÓN DE ESTADOS (Ajusta los IDs según tu BD) ---
        // 9  = Asignados
        // 8 = No Asignados / En Bodega (AJUSTA ESTE ID)
        // 12 = En Reparación 
        $asignados = $activosPorEstado[9] ?? 0;
        $noAsignados = $activosPorEstado[8] ?? 0; 
        $enReparacion = $activosPorEstado[12] ?? 0;
        
        $porcentajeAsignados = $totalActivos > 0 ? round(($asignados / $totalActivos) * 100) : 0;

        // 3. Valor Total del Inventario 
        $valorInventario = InvActivo::join('inv_detalle_compras', 'inv_activos.id_InvDetalleCompras', '=', 'inv_detalle_compras.id')
                                    ->sum('inv_detalle_compras.precio_unitario');

        // 4. Últimos movimientos
        $ultimosMovimientos = InvMovimiento::with(['responsable', 'tipoRegistro'])
                                           ->latest()
                                           ->take(5)
                                           ->get();

        // -----------------------------------------------------------------
        // 5. LÓGICA DE TABLA DE DETALLE (FILTROS)
        // -----------------------------------------------------------------
        $listaDetalle = null;
        $tituloDetalle = '';

        if ($request->has('filtro')) {
            $queryDetalle = InvActivo::with(['estado']); // Carga la relación 'estado'

            if ($request->filtro === 'asignados') {
                $queryDetalle->where('id_Estado', 9);
                $tituloDetalle = 'Equipos Asignados (' . $asignados . ')';
            } 
            elseif ($request->filtro === 'no_asignados') { // <-- NUEVA CONDICIÓN
                $queryDetalle->where('id_Estado', 8);     // (AJUSTA ESTE ID)
                $tituloDetalle = 'Equipos No Asignados (' . $noAsignados . ')';
            }
            elseif ($request->filtro === 'reparacion') {
                $queryDetalle->where('id_Estado', 12);
                $tituloDetalle = 'Equipos en Reparación (' . $enReparacion . ')';
            } 
            elseif ($request->filtro === 'todos') {
                $tituloDetalle = 'Todos los Equipos (' . $totalActivos . ')';
            }

            // Paginamos y mantenemos el parámetro en la URL
            $listaDetalle = $queryDetalle->latest()->paginate(10)->appends(request()->query());
        }

        return view('inventario.tablero.index', compact(
            'totalActivos', 
            'porcentajeAsignados', 
            'asignados',      // <-- Lo pasamos para mostrar la cantidad exacta si quieres
            'noAsignados',    // <-- NUEVO
            'enReparacion', 
            'valorInventario', 
            'ultimosMovimientos',
            'labelsGrafica',
            'dataGrafica',
            'listaDetalle',
            'tituloDetalle'
        ));
    }
}