<?php

namespace App\Http\Controllers\Rsv;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

// Importar todos los modelos necesarios para las pestañas
use App\Models\Rsv\CatalogoInmueble;
use App\Models\Rsv\Reserva;
use App\Models\Rsv\TransaccionFinanciera;
use App\Models\Rsv\Pasarela;
use App\Models\Rsv\AuditLog;

class AdminDashboardController extends Controller
{
    public function index(Request $request)
    {
        // 1. Pestaña: Inmuebles (Cargamos multimedia y tarifas)
        $inmuebles = CatalogoInmueble::with(['multimedia', 'tarifasTemporadas'])
            ->paginate(10, ['*'], 'page_inmuebles');

        // 2. Pestaña: Reservas (Cargamos usuario, inmueble y estado)
        $reservas = Reserva::with(['inmueble', 'user', 'status'])
            ->orderBy('created_at', 'desc')
            ->paginate(5, ['*'], 'page_reservas');

        // 4. Pestaña: Finanzas (Transacciones financieras con sus relaciones)
        $transacciones = TransaccionFinanciera::with(['reserva.inmueble', 'pasarela'])
            ->orderBy('created_at', 'desc')
            ->paginate(10, ['*'], 'page_finanzas');

        // Listado de Pasarelas de Pago para la pestaña de configuraciones
        $pasarelas = Pasarela::all();

        // 5. Pestaña: Auditoría
        $auditoria = AuditLog::latest()->paginate(10, ['*'], 'page_auditoria');

        // Retorna la vista principal pasando todas las variables compactadas
        return view('rsv.admin.dashboard', compact(
            'inmuebles',
            'reservas',
            'transacciones',
            'pasarelas',
            'auditoria'
        ));
    }
}
