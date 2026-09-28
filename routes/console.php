<?php

use App\Models\Interacciones\IntAlertaConfig;
use Carbon\Carbon;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote')->hourly();

Schedule::command('reservas:cancelar-vencidas')->daily();

// Corre solo: Cloud Scheduler llama a /internal/schedule-run cada minuto (ver
// SchedulerRunController), que dispara `php artisan schedule:run` en el servidor. Las alertas
// de contratos (sgrh.contrato.alertas) no dependen de este comando — calculan "vencido" en vivo.
Schedule::command('sgrh:marcar-contratos-vencidos')->daily();

// Alertas de Interacciones (Daytrack) — igual que arriba, corren solas vía Cloud Scheduler.
// Cada comando también se puede disparar a mano (`php artisan interacciones:...`) para pruebas.
//
// Los horarios NO están fijos en el código: se leen de int_alerta_config (editable desde Admin →
// Configuración de Alertas de Interacciones). Esto no es un "leer una vez al desplegar" — Laravel
// vuelve a evaluar routes/console.php completo en CADA tick de `schedule:run`, así que un cambio
// guardado en la pantalla de configuración se refleja en el próximo minuto, sin reiniciar nada.
$alertaConfig = IntAlertaConfig::actual();

Schedule::command('interacciones:alertar-vencidas-diario')
    ->weekdays()
    ->dailyAt(Carbon::parse($alertaConfig->correo_diario_hora)->format('H:i'))
    ->timezone('America/Bogota');

Schedule::command('interacciones:informe-semanal-areas')
    ->weeklyOn($alertaConfig->informe_semanal_dia, Carbon::parse($alertaConfig->informe_semanal_hora)->format('H:i'))
    ->timezone('America/Bogota');

Schedule::command('interacciones:alertar-inactividad-diaria')
    ->weekdays()
    ->dailyAt(Carbon::parse($alertaConfig->inactividad_hora)->format('H:i'))
    ->timezone('America/Bogota');

// Cierre automático de Soportes (ver CierreAutomaticoService) — corre solo cada hora vía Cloud
// Scheduler. ScpSoporteController::index() también lo dispara como respaldo (con caché de 1
// hora, para no pagar la consulta en cada visita), por si el scheduler llegara a fallar.
Schedule::command('soportes:cerrar-automatico')->hourly();
