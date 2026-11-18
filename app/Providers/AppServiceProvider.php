<?php

namespace App\Providers;

use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Route;
use App\Models\Maestras\MaeTerceros;
use Illuminate\Support\ServiceProvider;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Auth;
use App\Models\Rsv\AuditLog;
use App\Http\Controllers\Rsv\AuditLogController;

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

        Blade::if('candirect', function ($permiso) {
            return Auth::check() && Auth::user()->getDirectPermissions()->pluck('name')->contains($permiso);
        });

        // =====================================================================
        // AUDITORÍA AUTOMÁTICA GLOBAL DE GESTIÓN (MÓDULO RSV)
        // Captura CREATED, UPDATED y DELETED tanto de Cliente como de Admin
        // =====================================================================
        Event::listen('eloquent.created: App\Models\Rsv\*', function (string $eventName, array $data) {
            $model = $data[0] ?? null;
            if (!$model || $model instanceof AuditLog) {
                return;
            }
            AuditLogController::registrar(
                $model->getTable(),
                'CREATED',
                $model->getKey() ?? 0,
                null,
                $model->attributesToArray(),
                0
            );
        });

        Event::listen('eloquent.updated: App\Models\Rsv\*', function (string $eventName, array $data) {
            $model = $data[0] ?? null;
            if (!$model || $model instanceof AuditLog) {
                return;
            }
            $cambios = $model->getChanges();
            unset($cambios['updated_at']);
            if (empty($cambios)) {
                return;
            }
            $anteriores = [];
            foreach (array_keys($cambios) as $campo) {
                $anteriores[$campo] = $model->getOriginal($campo);
            }
            AuditLogController::registrar(
                $model->getTable(),
                'UPDATED',
                $model->getKey() ?? 0,
                $anteriores,
                $cambios,
                0
            );
        });

        Event::listen('eloquent.deleted: App\Models\Rsv\*', function (string $eventName, array $data) {
            $model = $data[0] ?? null;
            if (!$model || $model instanceof AuditLog) {
                return;
            }
            AuditLogController::registrar(
                $model->getTable(),
                'DELETED',
                $model->getKey() ?? 0,
                $model->attributesToArray(),
                ['eliminado_en' => now()->toDateTimeString()],
                0
            );
        });
    }
}
