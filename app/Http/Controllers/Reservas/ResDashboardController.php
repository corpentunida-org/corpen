<?php

namespace App\Http\Controllers\Reservas;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Reserva\Res_reserva;
use App\Models\Reserva\Res_inmueble;
use Illuminate\Routing\Controllers\HasMiddleware;

class ResDashboardController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return ['auth'];
    }

    public function index()
    {
        // 1. Total de inmuebles activos
        $total_inmuebles = Res_inmueble::where('active', 1)->count();
        
        // 2. Reservas en estado pendiente (Status 1: Solicitada, Status 5: Con soporte subido)
        $reservas_pendientes = Res_reserva::whereIn('res_status_id', [1, 5])->count();
        
        // 3. Reservas confirmadas (Status 2: Confirmada)
        $reservas_confirmadas = Res_reserva::where('res_status_id', 2)->count();
        
        // 4. Obtener las últimas 5 reservas registradas para la tabla resumen
        $ultimas_reservas = Res_reserva::with(['res_inmueble', 'user', 'res_status'])
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();

        // Enviamos los datos a la vista del dashboard
        return view('reserva.dashboard.index', compact(
            'total_inmuebles', 
            'reservas_pendientes', 
            'reservas_confirmadas', 
            'ultimas_reservas'
        ));
    }
}