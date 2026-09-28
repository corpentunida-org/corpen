<?php

namespace App\Console\Commands\Soportes;

use App\Services\Soportes\CierreAutomaticoService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class CerrarTicketsAutomatico extends Command
{
    protected $signature = 'soportes:cerrar-automatico';

    protected $description = 'Cierra los soportes que llevan 5 días seguidos en estado "En Revisión" sin moverse';

    public function handle(CierreAutomaticoService $servicio)
    {
        Log::info('Scheduler soportes:cerrar-automatico ejecutado');

        $total = $servicio->ejecutar();

        $this->info($total.' soporte(s) cerrado(s) automáticamente.');
    }
}
