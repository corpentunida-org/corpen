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

        // 2. Activos por Estado (Usamos pluck para obtener un array de clave = id_Estado, valor = total)
        $activosPorEstado = InvActivo::selectRaw('id_Estado, count(*) as total')
                                     ->groupBy('id_Estado')
                                     ->pluck('total', 'id_Estado');

        // Extraemos los valores específicos usando el ID del estado (2 = Asignados, 4 = En Reparación)
        $asignados = $activosPorEstado[9] ?? 0;
        $enReparacion = $activosPorEstado[12] ?? 0;
        
        // Calculamos el porcentaje de forma segura
        $porcentajeAsignados = $totalActivos > 0 ? round(($asignados / $totalActivos) * 100) : 0;

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
            'ultimosMovimientos'
        ));
    }
}