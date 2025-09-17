<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CanDirect
{
    /**
     * Maneja una solicitud entrante.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     * @param  string  ...$permissions
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function handle(Request $request, Closure $next, ...$permissions)
    {
        $user = Auth::user();
        $directPermissions = $user->getDirectPermissions()->pluck('name')->toArray();

        foreach ($permissions as $permission) {
            if (in_array($permission, $directPermissions)) {
                return $next($request);
            }
        }

        abort(404, 'Pagina no encontrada.');
    }
}



