<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class ContabilidadMantenimiento
{
    public function handle(Request $request, Closure $next)
    {
        // 1. Revisamos si el mantenimiento está activado en la Cache
        $mantenimientoActivo = Cache::get('contabilidad_mantenimiento_active', false);

        // 2. Si está activo, bloqueamos el acceso
        if ($mantenimientoActivo) {
            // Rutas que SIEMPRE deben estar disponibles (para que no haya bucles infinitos)
            $rutasPermitidas = [
                'contabilidad.mantenimiento',        // La vista profesional
                'contabilidad.mantenimiento.toggle', // El switch para apagarlo
                'contabilidad.sincronizar.index',    // Tu consola de Admin
                'contabilidad.sincronizar.subir',
                'contabilidad.sincronizar.confirmar',
            ];

            // Si la ruta actual NO está en las permitidas, redirigir a mantenimiento
            if (!$request->routeIs($rutasPermitidas) && $request->routeIs('contabilidad.*')) {
                return redirect()->route('contabilidad.mantenimiento');
            }
        }

        return $next($request);
    }
}