<?php

namespace App\Http\Controllers\Sgrh;

use App\Http\Controllers\Controller;
use App\Models\Sgrh\VacacionSolicitud;
use App\Services\Sgrh\VacacionAprobadorResolver;
use Illuminate\Support\Facades\Auth;

class VacacionCalendarioController extends Controller
{
    public function __construct(private VacacionAprobadorResolver $resolver)
    {
    }

    public function index()
    {
        return view('sgrh.vacacion.calendario.index');
    }

    /**
     * Endpoint JSON dedicado para FullCalendar — a diferencia del patrón usado en Reservas
     * (eventos inline renderizados en Blade), esto evita recargar toda la página cada vez que
     * cambie el rango visible del calendario, importante con el volumen de solicitudes que
     * puede acumular RRHH viendo a toda la empresa.
     */
    public function eventos()
    {
        $solicitudes = $this->resolver->solicitudesVisiblesPara(Auth::user())
            ->whereIn('estado', ['pendiente', 'aprobada'])
            ->with('empleado.tercero')
            ->get();

        $colores = ['pendiente' => '#f59e0b', 'aprobada' => '#22c55e'];

        $eventos = $solicitudes->map(fn (VacacionSolicitud $s) => [
            'title' => $s->empleado->nombre_completo . ($s->estado === 'pendiente' ? ' (pendiente)' : ''),
            'start' => $s->fecha_inicio->format('Y-m-d'),
            'end' => $s->fecha_fin->copy()->addDay()->format('Y-m-d'), // FullCalendar trata 'end' como exclusivo
            'color' => $colores[$s->estado] ?? '#64748b',
            'extendedProps' => [
                'id' => $s->id,
                'dias' => (float) $s->dias_habiles,
                'estado' => $s->estado,
            ],
        ]);

        return response()->json($eventos);
    }
}
