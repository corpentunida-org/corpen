<?php

namespace App\Http\Controllers;

use App\Models\Reserva\Res_inmueble;
use App\Models\Reserva\Res_reserva;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;

class ResReservaController extends Controller  implements HasMiddleware
{
    use AuthorizesRequests;
    //
    public static function middleware(): array
    {
        return [
            'auth',
        ];
    }

    public function index()
    {
        $reservas = Res_reserva::where('user_id', auth()->user()->id)
            ->orderBy('fecha_inicio', 'desc')
            ->get();
        $inmuebles = Res_inmueble::where('active', 1)
            ->orderBy('name', 'asc')
            ->get();
        return view('reserva.asociado.index', compact('reservas', 'inmuebles'));
    }

    public function createReserva ( $id )
    {
        $inmueble = Res_inmueble::findOrFail($id);
        return view('reserva.asociado.create', compact('inmueble'));
    }
}
