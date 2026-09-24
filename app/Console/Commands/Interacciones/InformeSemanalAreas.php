<?php

namespace App\Console\Commands\Interacciones;

use App\Mail\Interacciones\InteraccionesInformeSemanalMail;
use App\Services\Interacciones\AlertasInteraccionesService;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class InformeSemanalAreas extends Command
{
    protected $signature = 'interacciones:informe-semanal-areas {--dry-run : Solo mostrar a quién se le enviaría, sin enviar correos de verdad}';

    protected $description = 'Envía a los admon de cada área un informe semanal (lunes) con la gestión de TODOS sus agentes, incluidos los que no registraron nada';

    public function handle(AlertasInteraccionesService $servicio)
    {
        Log::info('Scheduler interacciones:informe-semanal-areas ejecutado');
        $dryRun = (bool) $this->option('dry-run');

        // Semana pasada completa (lunes a domingo) — si esto corre el lunes en la mañana, "ayer"
        // es domingo, y el inicio de esa semana es el lunes anterior.
        $hasta = Carbon::yesterday()->endOfDay();
        $desde = $hasta->copy()->startOfWeek(Carbon::MONDAY)->startOfDay();

        $areasEnviadas = 0;
        $areasSinAdmin = [];

        foreach ($servicio->areasConInteracciones() as $area) {
            $admins = $servicio->adminsDelArea($area);
            if ($admins->isEmpty()) {
                $areasSinAdmin[] = $area;

                continue;
            }

            $usuarios = $servicio->usuariosDelArea($area);
            // Clave del pedido: TODOS los agentes del área salen en la fila, así no hayan
            // registrado nada esta semana (en ese caso el total queda en 0, no se omiten).
            $filas = $usuarios->map(function ($usuario) use ($servicio, $desde, $hasta) {
                return (object) [
                    'usuario' => $usuario,
                    'total' => $servicio->interaccionesDelUsuarioEntre($usuario->id, $desde, $hasta),
                    'vencidas' => $servicio->contarVencidasDeUsuario($usuario->id),
                ];
            })->sortByDesc('total')->values();

            if ($dryRun) {
                $this->line("[dry-run] Área {$area} -> ".$admins->pluck('email')->implode(', ').' ('.$filas->count().' agentes, '.$filas->where('total', 0)->count().' sin actividad)');
            } else {
                Mail::to($admins->pluck('email')->all())
                    ->send(new InteraccionesInformeSemanalMail($area, $filas, $desde, $hasta));
            }

            $areasEnviadas++;
        }

        $verbo = $dryRun ? 'Se enviarían' : 'Informes semanales enviados';
        $this->info("{$verbo} a {$areasEnviadas} área(s).");
        if (! empty($areasSinAdmin)) {
            $this->warn('Áreas sin admin configurado (no se envió informe): '.implode(', ', $areasSinAdmin));
        }
    }
}
