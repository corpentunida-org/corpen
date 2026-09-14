<?php

namespace App\Listeners;

use App\Models\UserSesion;
use Illuminate\Auth\Events\Login;

class RegistrarInicioSesion
{
    public function handle(Login $event): void
    {
        $ahora = now();

        UserSesion::create([
            'user_id' => $event->user->id,
            'login_at' => $ahora,
            'last_activity_at' => $ahora,
            'ip' => request()->ip(),
        ]);
    }
}
