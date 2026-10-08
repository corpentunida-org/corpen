<?php

namespace App\Http\Controllers\Rsv;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

// Importa los modelos que vas a usar en las pestañas
use App\Models\Rsv\CatalogoInmueble;
use App\Models\Rsv\Reserva;
use App\Models\Rsv\TransaccionFinanciera;
use App\Models\Rsv\AuditLog;

class AdminDashboardController extends Controller
{
    public function index(Request $request)
    {
        // 1. Pestaña: Inmuebles (Cargamos multimedia y tarifas para que el carrusel y precios funcionen)
        $inmuebles = CatalogoInmueble::with(['multimedia', 'tarifasTemporadas'])
            ->paginate(10, ['*'], 'page_inmuebles');

        // 2. Pestaña: Reservas (Cargamos el usuario, el inmueble y el estado para pintar los badges y nombres)
        $reservas = Reserva::with(['inmueble', 'user', 'status'])
            ->orderBy('created_at', 'desc') // Las más recientes primero
            ->paginate(5, ['*'], 'page_reservas');

        // 3. Pestaña: Finanzas
        // Si no tienes registros o el modelo está vacío, esto simplemente pasará un paginador vacío.
        $finanzas = TransaccionFinanciera::paginate(10, ['*'], 'page_finanzas');

        // 4. Pestaña: Auditoría
        $auditoria = AuditLog::latest()->paginate(10, ['*'], 'page_auditoria');

        // 5. Pestaña: Calendario Global (No necesita paginación, normalmente se carga vía AJAX o pasas las reservas en JSON)
        // Lo dejamos preparado por si usas FullCalendar.

        // Retorna la vista principal pasando todas las variables
        return view('rsv.admin.dashboard', compact('inmuebles', 'reservas', 'finanzas', 'auditoria'));
    }
}
