<?php

namespace App\Console\Commands\Sgrh;

use App\Models\Sgrh\Contrato;
use App\Models\Sgrh\Empleado;
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
        $contratosVencidos = Contrato::where('estado', 'Activo')
            ->whereNotNull('fecha_vencimiento')
            ->where('fecha_vencimiento', '<', now())
            ->get(['id', 'empleado_id']);

        if ($contratosVencidos->isEmpty()) {
            $this->info('No hay contratos vencidos por marcar.');

            return;
        }

        $ids = $contratosVencidos->pluck('id');
        Contrato::whereIn('id', $ids)->update(['estado' => 'Vencido']);

        // No puede haber colaboradores activos sin contrato activo: cada colaborador afectado
        // se queda sin contrato vigente al perder este (misma regla que
        // ContratoController::sincronizarEstadoColaborador(), duplicada aquí porque este
        // comando de consola no puede llamar a un método de ese controlador).
        $empleadosInactivados = Empleado::whereIn('id', $contratosVencidos->pluck('empleado_id')->unique())
            ->where('estado', 'activo')
            ->get()
            ->filter(fn(Empleado $empleado) => !$empleado->contratoActivo)
            ->each(fn(Empleado $empleado) => $empleado->update(['estado' => 'inactivo']));

        // AuditoriaController::create() exige un usuario autenticado (Auth::user()->name) y no
        // aplica en un comando de consola/cron — mismo motivo por el que
        // reservas:cancelar-vencidas usa Log::info() en vez de la tabla Auditoria.
        Log::info("sgrh:marcar-contratos-vencidos marcó {$ids->count()} contrato(s) como Vencido: " . $ids->implode(', ')
            . ". Colaboradores inactivados: " . $empleadosInactivados->pluck('id')->implode(', '));

        $this->info("{$ids->count()} contrato(s) marcados como Vencido. {$empleadosInactivados->count()} colaborador(es) inactivados.");
    }
}
