<?php

namespace App\Http\Controllers\Inventario;

use App\Http\Controllers\Controller;
use App\Models\Inventario\InvActivo;
use App\Models\Inventario\InvMovimiento;
use Illuminate\Http\Request;

class TableroInventarioController extends Controller
{
    public function index()
    {
        // 1. Total de Activos
        $totalActivos = InvActivo::count();

        // 2. Consultamos agrupando e incluyendo la relación 'estado' para los nombres de la gráfica
        $activosAgrupados = InvActivo::selectRaw('id_Estado, count(*) as total')
                                     ->with('estado')
                                     ->groupBy('id_Estado')
                                     ->get();

        // Preparamos los datos para la gráfica y recreamos tu array original $activosPorEstado
        $labelsGrafica = [];
        $dataGrafica = [];
        $activosPorEstado = [];

        foreach ($activosAgrupados as $item) {
            // Llenamos para la gráfica
            $labelsGrafica[] = $item->estado ? $item->estado->nombre : 'Estado ' . $item->id_Estado;
            $dataGrafica[]   = $item->total;
            
            // Recreamos tu array [id_Estado => total] para no dañar tu lógica
            $activosPorEstado[$item->id_Estado] = $item->total;
        }

        // --- TU CÓDIGO INTACTO AQUÍ ---
        // Extraemos los valores específicos usando el ID del estado (9 = Asignados, 12 = En Reparación)
        $asignados = $activosPorEstado[9] ?? 0;
        $enReparacion = $activosPorEstado[12] ?? 0;
        
        // Calculamos el porcentaje de forma segura
        $porcentajeAsignados = $totalActivos > 0 ? round(($asignados / $totalActivos) * 100) : 0;
        // ------------------------------

        // 3. Valor Total del Inventario 
        // Unimos los activos con su detalle de compra para sumar el precio unitario de cada activo existente.
        $valorInventario = InvActivo::join('inv_detalle_compras', 'inv_activos.id_InvDetalleCompras', '=', 'inv_detalle_compras.id')
                                    ->sum('inv_detalle_compras.precio_unitario');

        // 4. Últimos movimientos (Eager loading para evitar el problema N+1)
        // Asegúrate de que la relación 'tipoRegistro' exista en tu modelo InvMovimiento
        $ultimosMovimientos = InvMovimiento::with(['responsable', 'tipoRegistro'])
                                           ->latest()
                                           ->take(5)
                                           ->get();

        return view('inventario.tablero.index', compact(
            'totalActivos', 
            'porcentajeAsignados', 
            'enReparacion', 
            'valorInventario', 
            'ultimosMovimientos',
            'labelsGrafica', // <-- Agregado para la gráfica
            'dataGrafica'    // <-- Agregado para la gráfica
        ));
    }
}