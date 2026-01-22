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

        // 2. Activos por Estado (para gráfica)
        $activosPorEstado = InvActivo::selectRaw('id_Estado, count(*) as total')
                                     ->groupBy('id_Estado')
                                     ->with('estado')
                                     ->get();

        // 3. Valor Total del Inventario (Calculado desde compras)
        // Esto es un ejemplo, requeriría un join más complejo si el precio está en detalle_compra
        // Por simplicidad, asumamos que lo traes o lo calculas
        
        // 4. Últimos movimientos
        $ultimosMovimientos = InvMovimiento::with('responsable')->latest()->take(5)->get();

        return view('inventario.tablero.index', compact('totalActivos', 'activosPorEstado', 'ultimosMovimientos'));
    }
}