<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Antes, el "tiempo trabajado" solo vivía en interactions.duration — un único número que se
     * REEMPLAZA (no se suma) en cada edición, así que nunca reflejó el total real invertido a
     * través de varias gestiones, solo la última sesión. Ahora que los informes cuentan por
     * int_seguimiento (cada gestión = una interacción para efectos de reporte, ver
     * InteractionController::report()), el tiempo también necesita vivir aquí: cada seguimiento
     * guarda el tiempo NUEVO que esa gestión puntual contabilizó.
     *
     * Backfill: los seguimientos ya existentes no tienen ese dato por separado — se le asigna el
     * duration actual de su interacción al ÚLTIMO seguimiento de cada una (aproximación
     * razonable: es donde ese tiempo ya se contaba antes, ya que interactions.duration siempre
     * reflejó la sesión más reciente).
     */
    public function up(): void
    {
        Schema::table('int_seguimiento', function (Blueprint $table) {
            $table->unsignedInteger('duration')->nullable()->after('outcome');
        });

        DB::statement('
            UPDATE int_seguimiento s
            JOIN interactions i ON i.id = s.id_interaction
            SET s.duration = i.duration
            WHERE s.id = (
                SELECT max_id FROM (
                    SELECT MAX(id) as max_id FROM int_seguimiento WHERE id_interaction = s.id_interaction
                ) as ultimo
            )
            AND i.duration IS NOT NULL AND i.duration > 0
        ');
    }

    public function down(): void
    {
        Schema::table('int_seguimiento', function (Blueprint $table) {
            $table->dropColumn('duration');
        });
    }
};
