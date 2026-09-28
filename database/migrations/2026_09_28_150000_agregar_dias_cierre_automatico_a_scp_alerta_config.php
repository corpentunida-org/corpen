<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Antes el "5 días sin movimiento en Revisión = cierre automático" estaba fijo en
     * CierreAutomaticoService (Carbon::now()->subDays(5)) — ahora es configurable desde la nueva
     * pantalla "Configuración de Alertas (Soportes)", igual que el resto de estos parámetros.
     * Default 5 para no cambiar el comportamiento actual al desplegar esto.
     */
    public function up(): void
    {
        Schema::table('scp_alerta_config', function (Blueprint $table) {
            $table->unsignedTinyInteger('dias_cierre_automatico')->default(5)->after('dias_posponer_para_escalar');
        });
    }

    public function down(): void
    {
        Schema::table('scp_alerta_config', function (Blueprint $table) {
            $table->dropColumn('dias_cierre_automatico');
        });
    }
};
