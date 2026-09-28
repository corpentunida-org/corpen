<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Adjunto propio de CADA observación (evidencia de una respuesta puntual), separado del
     * adjunto original del ticket (scp_soportes.soporte). Necesario para
     * ScpSoporteController::descargarAdjuntoObservacion() — la ruta ya existía pero apuntaba a un
     * método inexistente, y scp_observaciones no tenía dónde guardar ese archivo.
     */
    public function up(): void
    {
        Schema::table('scp_observaciones', function (Blueprint $table) {
            $table->string('archivo')->nullable()->after('calcification');
        });
    }

    public function down(): void
    {
        Schema::table('scp_observaciones', function (Blueprint $table) {
            $table->dropColumn('archivo');
        });
    }
};
