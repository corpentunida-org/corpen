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
        $limite = Carbon::now()->subDays(4);
        $reservas = Res_reserva::whereNull('soporte_pago')
            ->where('res_status_id', 5)->where('fecha_solicitud', '<=', $limite)->get();
        foreach ($reservas as $reserva) {
            $reserva->update(['res_status_id' => 3]);
            $this->info("Reserva {$reserva->id} cancelada");
            $this->info('Proceso finalizado');
        }
    }
}
