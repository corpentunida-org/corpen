<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Dos ajustes nuevos:
     *  - recordatorio_matutino_hora (solo Interacciones): hora del aviso una vez al día con lo
     *    que vence HOY (no lo ya vencido, eso lo cubre el aviso forzado cada N horas).
     *  - pulso_intervalo_minutos (Interacciones y Soportes): cada cuánto se anima el ícono del
     *    header (tamaño + color + sonido corto) mientras haya algo pendiente, sin interrumpir.
     */
    public function up(): void
    {
        Schema::table('int_alerta_config', function (Blueprint $table) {
            $table->time('recordatorio_matutino_hora')->default('08:00:00')->after('aviso_intervalo_horas');
            $table->unsignedTinyInteger('pulso_intervalo_minutos')->default(10)->after('recordatorio_matutino_hora');
        });

        Schema::table('scp_alerta_config', function (Blueprint $table) {
            $table->unsignedTinyInteger('pulso_intervalo_minutos')->default(10)->after('aviso_intervalo_horas');
        });
    }

    public function down(): void
    {
        Schema::table('int_alerta_config', function (Blueprint $table) {
            $table->dropColumn(['recordatorio_matutino_hora', 'pulso_intervalo_minutos']);
        });

        Schema::table('scp_alerta_config', function (Blueprint $table) {
            $table->dropColumn('pulso_intervalo_minutos');
        });
    }
};
