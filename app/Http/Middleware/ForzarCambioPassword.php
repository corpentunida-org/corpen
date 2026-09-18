<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

/**
 * Cuando un admin le asigna contraseña a un usuario desde Gestión de Usuarios marcando "forzar
 * cambio en el próximo inicio de sesión" (debe_cambiar_password), este middleware lo intercepta
 * en cualquier módulo — empleado o asociado, da igual por dónde entre — y no lo deja hacer nada
 * más hasta que cambie la contraseña. Sin esto, la casilla del admin no tendría ningún efecto
 * real más allá de guardarse en la base de datos.
 */
class ForzarCambioPassword
{
    private const RUTAS_PERMITIDAS = [
        'password.forzar',
        'password.forzar.store',
        'logout',
    ];

    public function handle(Request $request, Closure $next): Response
    {
        $user = Auth::user();

        if ($user && $user->debe_cambiar_password && !in_array($request->route()?->getName(), self::RUTAS_PERMITIDAS, true)) {
            return redirect()->route('password.forzar');
        }

        return $next($request);
    }
}
