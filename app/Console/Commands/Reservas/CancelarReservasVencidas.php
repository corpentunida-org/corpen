<?php

namespace App\Console\Commands\Reservas;

use Illuminate\Console\Command;
use App\Models\Reserva\Res_reserva;
use Carbon\Carbon;

class CancelarReservasVencidas extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'reservas:cancelar-vencidas';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Cancela reservas sin soporte de pago después de 4 días';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        \Log::info('Scheduler cancelar reservas ejecutado');
        $limite = now()->subDays(4);
        $canceladas = Res_reserva::whereNull('soporte_pago')
            ->where('res_status_id', 5)
            ->where('fecha_solicitud', '<=', $limite)
            ->update([
                'res_status_id' => 3,
                'deleted_at' => now()
            ]);

        $this->info("$canceladas reservas canceladas automáticamente");
    }
}
