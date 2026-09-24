<?php

namespace App\Console\Commands\Interacciones;

use App\Mail\Interacciones\InteraccionesVencidasDiarioMail;
use App\Services\Interacciones\AlertasInteraccionesService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class AlertarVencidasDiario extends Command
{
    protected $signature = 'interacciones:alertar-vencidas-diario {--dry-run : Solo mostrar a quién se le enviaría, sin enviar correos de verdad}';

    protected $description = 'Envía un correo diario a cada agente con interacciones que tiene vencidas hoy';

    public function handle(AlertasInteraccionesService $servicio)
    {
        Log::info('Scheduler interacciones:alertar-vencidas-diario ejecutado');
        $dryRun = (bool) $this->option('dry-run');

        $usuarios = $servicio->usuariosConAccesoInteracciones();
        $enviados = 0;

        foreach ($usuarios as $usuario) {
            if (! $usuario->email) {
                continue;
            }

            $vencidas = $servicio->vencidasDeUsuario($usuario->id);
            if ($vencidas->isEmpty()) {
                continue;
            }

            if ($dryRun) {
                $this->line("[dry-run] {$usuario->name} <{$usuario->email}> — {$vencidas->count()} vencida(s)");
            } else {
                Mail::to($usuario->email)->send(new InteraccionesVencidasDiarioMail($usuario, $vencidas));
            }
            $enviados++;
        }

        $verbo = $dryRun ? 'Se enviarían' : 'Correos de vencidas enviados';
        $this->info("{$verbo}: {$enviados} de {$usuarios->count()} usuarios con acceso a Interacciones.");
    }
}
