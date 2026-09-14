<?php

namespace App\Http\Middleware;

use App\Models\UserSesion;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * Mantiene "last_activity_at" al día en la sesión abierta del usuario, para poder estimar tiempo
 * activo aunque nunca cierre sesión explícitamente (ver App\Listeners\RegistrarFinSesion). Se
 * limita a un UPDATE cada 60s por usuario (vía la sesión HTTP, sin ir a BD) para no escribir en
 * cada request.
 */
class RegistrarActividadUsuario
{
    private const INTERVALO_SEGUNDOS = 60;

    public function handle(Request $request, Closure $next)
    {
        if (Auth::check()) {
            $ultimoRegistro = $request->session()->get('actividad_registrada_en');

            if (!$ultimoRegistro || now()->diffInSeconds($ultimoRegistro) >= self::INTERVALO_SEGUNDOS) {
                $userId = Auth::id();

                $sesion = UserSesion::where('user_id', $userId)
                    ->whereNull('logout_at')
                    ->latest('login_at')
                    ->first();

                // Usuario con sesión de Laravel activa desde antes de desplegar esta función:
                // no hay fila de sesiones_usuario todavía, se crea una para no perder el resto
                // de su actividad (el login real no se pudo capturar, pero sí lo de aquí en
                // adelante).
                if (!$sesion) {
                    $sesion = UserSesion::create([
                        'user_id' => $userId,
                        'login_at' => now(),
                        'last_activity_at' => now(),
                        'ip' => $request->ip(),
                    ]);
                } else {
                    $sesion->update(['last_activity_at' => now()]);
                }

                $request->session()->put('actividad_registrada_en', now());
            }
        }

        return $next($request);
    }
}
