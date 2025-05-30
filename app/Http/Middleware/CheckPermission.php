<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckPermission
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        dd("Middleware ejecutado", auth()->user(), $permission);
        $user = auth()->user(); // Obtener el usuario autenticado

        if (!$user || !$user->hasPermissionTo($permission)) {
            return abort(403, 'No tienes permiso para acceder a esta página');
        }

        return $next($request);
    }
}
