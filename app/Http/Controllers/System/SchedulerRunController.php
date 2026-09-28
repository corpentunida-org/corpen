<?php

namespace App\Http\Controllers\System;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Log;

/**
 * Endpoint HTTP para disparar `php artisan schedule:run` en plataformas sin cron/supervisor
 * propio (ej. Google Cloud Run), donde se configura Google Cloud Scheduler para llamar esta URL
 * cada minuto. Reemplaza al cron del sistema; ver también el servicio `scheduler` en
 * docker-compose.yml (ese es solo para pruebas locales de rama, no para producción).
 *
 * Protegido por un token compartido (config('services.scheduler_token'), nunca env() directo
 * aquí — ver el comentario en config/services.php) enviado en el header `X-Scheduler-Token`.
 */
class SchedulerRunController extends Controller
{
    public function __invoke(Request $request)
    {
        $tokenEsperado = config('services.scheduler_token');

        if (blank($tokenEsperado)) {
            // Sin token configurado, el endpoint queda deshabilitado por seguridad (nunca abierto
            // por accidente en un despliegue donde falte la variable de entorno).
            abort(503, 'Scheduler endpoint no configurado.');
        }

        $tokenRecibido = (string) $request->header('X-Scheduler-Token', $request->query('token', ''));

        if (! hash_equals((string) $tokenEsperado, $tokenRecibido)) {
            abort(403, 'Token inválido.');
        }

        Artisan::call('schedule:run');
        $salida = Artisan::output();

        Log::channel(config('logging.default'))->info('schedule:run disparado vía HTTP', [
            'ip' => $request->ip(),
            'salida' => trim($salida),
        ]);

        return response()->json([
            'ok' => true,
            'ejecutado_en' => now()->toDateTimeString(),
            'salida' => trim($salida),
        ]);
    }
}
