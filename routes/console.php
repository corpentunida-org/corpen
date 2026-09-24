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

// Nota: este proyecto no tiene infraestructura de cron/supervisor invocando
// `php artisan schedule:run` (verificado — ver riesgo 6 de la Fase 7 de SGRH en el plan). El
// registro aquí deja el comando listo para cuando esa infraestructura se agregue; mientras
// tanto no se ejecuta solo. Las alertas de contratos (sgrh.contrato.alertas) no dependen de
// este comando — calculan "vencido" en vivo.
Schedule::command('sgrh:marcar-contratos-vencidos')->daily();

// Alertas de Interacciones (Daytrack) — igual que arriba, quedan registradas pero NO se
// ejecutan solas hasta que el servidor real tenga un cron/supervisor llamando
// `php artisan schedule:run` cada minuto. Mientras tanto, cada comando se puede disparar a mano
// (`php artisan interacciones:...`) para probarlo o para cubrir el envío del día manualmente.
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
