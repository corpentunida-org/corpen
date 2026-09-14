<?php

namespace App\Listeners;

use App\Models\UserSesion;
use Illuminate\Auth\Events\Logout;

class RegistrarFinSesion
{
    public function handle(Logout $event): void
    {
        if (!$event->user) {
            return;
        }

        // Se cierra la sesión abierta más reciente de este usuario, no una fila específica: no
        // hace falta rastrear el id de sesión de actividad a través del ciclo de logout (que
        // invalida la sesión HTTP justo después de este evento).
        UserSesion::where('user_id', $event->user->id)
            ->whereNull('logout_at')
            ->latest('login_at')
            ->first()
            ?->update(['logout_at' => now()]);
    }
}
