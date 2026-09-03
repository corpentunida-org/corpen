<?php

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
