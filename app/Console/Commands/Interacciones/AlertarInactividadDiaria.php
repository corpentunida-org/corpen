<?php

namespace App\Console\Commands\Interacciones;

use App\Mail\Interacciones\InteraccionesInactividadMail;
use App\Models\Interacciones\IntAlertaOmitido;
use App\Services\Interacciones\AlertasInteraccionesService;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class AlertarInactividadDiaria extends Command
{
    protected $signature = 'interacciones:alertar-inactividad-diaria {--dry-run : Solo mostrar a quién se le enviaría, sin enviar correos de verdad}';

    protected $description = 'Avisa a los admon de cada área qué usuarios llevan el día (lunes a viernes) sin registrar ninguna interacción';

    public function handle(AlertasInteraccionesService $servicio)
    {
        Log::info('Scheduler interacciones:alertar-inactividad-diaria ejecutado');
        $dryRun = (bool) $this->option('dry-run');

        $hoy = Carbon::today();

        // Defensa extra además de ->weekdays() en el scheduler: si algún día se dispara a mano un
        // sábado/domingo, no tiene sentido avisar de "inactividad" en día no hábil.
        if ($hoy->isWeekend()) {
            $this->info('Hoy es fin de semana — no se envía alerta de inactividad.');

            return;
        }

        $inicioHoy = $hoy->copy()->startOfDay();
        $finHoy = $hoy->copy()->endOfDay();

        $areasAlertadas = 0;
        // "Omitir Correos" (Admin → Configuración de Alertas → Agentes Omitidos) solo excluía al
        // correo de vencidas (que le llega directo al agente) — este correo de inactividad va a
        // los admon del área, no al agente, pero igual lo NOMBRA en la lista. Quien queda
        // exento de "correos" se entiende como exento de que Daytrack lo señale por email, no
        // solo de recibir uno él mismo, así que también se excluye aquí.
        $omitidosCorreo = IntAlertaOmitido::idsOmitidos('correo');

        foreach ($servicio->areasConInteracciones() as $area) {
            $usuarios = $servicio->usuariosDelArea($area);

            $inactivos = $usuarios->filter(function ($usuario) use ($servicio, $inicioHoy, $finHoy, $omitidosCorreo) {
                if (in_array($usuario->id, $omitidosCorreo, true)) {
                    return false;
                }

                return $servicio->interaccionesDelUsuarioEntre($usuario->id, $inicioHoy, $finHoy) === 0;
            })->values();

            if ($inactivos->isEmpty()) {
                continue;
            }

            $admins = $servicio->adminsDelArea($area);
            if ($admins->isEmpty()) {
                continue;
            }

            if ($dryRun) {
                $this->line("[dry-run] Área {$area} -> ".$admins->pluck('email')->implode(', ').' ('.$inactivos->count().' inactivos: '.$inactivos->pluck('name')->implode(', ').')');
            } else {
                Mail::to($admins->pluck('email')->all())
                    ->send(new InteraccionesInactividadMail($area, $inactivos, $hoy));
            }

            $areasAlertadas++;
        }

        $verbo = $dryRun ? 'Se enviarían' : 'Alertas de inactividad enviadas';
        $this->info("{$verbo} a {$areasAlertadas} área(s).");
    }
}
