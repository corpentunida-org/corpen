<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use App\Http\Middleware\CanDirect;
// Importamos el nuevo middleware
use App\Http\Middleware\ContabilidadMantenimiento;
use App\Http\Middleware\ForzarCambioPassword;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->trustProxies(at: '*');

        // Registramos ambos alias en el mismo array
        $middleware->alias([
            'candirect'           => CanDirect::class,
            'check.mantenimiento' => ContabilidadMantenimiento::class,
        ]);

        // Global (no por-ruta): la marca "forzar cambio de contraseña" que se asigna desde
        // Gestión de Usuarios debe interceptar a CUALQUIER usuario autenticado sin importar en
        // qué módulo esté — empleado o asociado — así que va en el grupo 'web' completo en vez
        // de tener que agregarla a cada uno de los muchos grupos de rutas de routes/web.php.
        $middleware->web(append: [
            ForzarCambioPassword::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();

/* use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use App\Http\Middleware\CanDirect;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->trustProxies(at: '*');
        $middleware->alias([
            'candirect' => CanDirect::class,
        ]);
        //
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
 */