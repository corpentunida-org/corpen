<?php

namespace App\Console\Commands\Sgrh;

use App\Models\Sgrh\Contrato;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class MarcarContratosVencidos extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sgrh:marcar-contratos-vencidos';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Marca como Vencido todo contrato Activo cuya fecha_vencimiento ya pasó';

    /**
     * Execute the console command.
     *
     * Nota: las alertas de vencimiento (sgrh.contrato.alertas) NO dependen de este comando —
     * calculan "vencido" en vivo desde fecha_vencimiento (Contrato::estaVencido). Este comando
     * solo mantiene el campo `estado` al día en la base de datos; si no llega a ejecutarse
     * (este proyecto no tiene infraestructura de cron/supervisor invocando
     * `php artisan schedule:run` todavía), las alertas siguen siendo correctas igual.
     */
    public function handle()
    {
        $ids = Contrato::where('estado', 'Activo')
            ->whereNotNull('fecha_vencimiento')
            ->where('fecha_vencimiento', '<', now())
            ->pluck('id');

        if ($ids->isEmpty()) {
            $this->info('No hay contratos vencidos por marcar.');

            return;
        }

        Contrato::whereIn('id', $ids)->update(['estado' => 'Vencido']);

        // AuditoriaController::create() exige un usuario autenticado (Auth::user()->name) y no
        // aplica en un comando de consola/cron — mismo motivo por el que
        // reservas:cancelar-vencidas usa Log::info() en vez de la tabla Auditoria.
        Log::info("sgrh:marcar-contratos-vencidos marcó {$ids->count()} contrato(s) como Vencido: " . $ids->implode(', '));

        $this->info("{$ids->count()} contrato(s) marcados como Vencido.");
    }
}
