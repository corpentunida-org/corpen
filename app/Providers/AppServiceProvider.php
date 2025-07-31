<?php

namespace App\Providers;

use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Route;          // ✅ Agregado
use App\Models\Maestras\maeTerceros;           // ✅ Agregado
use Illuminate\Support\ServiceProvider;
use Illuminate\Pagination\Paginator;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        Paginator::useBootstrap();

        if (Request::is('maestras/congregacion*')) {
            Paginator::defaultView('components.maestras.congregaciones.pagination');
        }

        // 👇 Esto es crucial para que {tercero} use cod_ter en vez del id
        Route::bind('tercero', function ($value) {
            return MaeTerceros::where('cod_ter', $value)->firstOrFail();
        });
    }
}
