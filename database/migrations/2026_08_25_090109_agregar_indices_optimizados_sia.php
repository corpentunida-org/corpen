<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Ejecuta las migraciones (Crea los índices).
     */
    public function up()
    {
        // Los bloques 1, 2 y 3 ya se ejecutaron con éxito en tu base de datos,
        // por eso solo dejamos el bloque 4 para terminar el trabajo.

        // 4. Alertas (Usando tu nombre exacto: car_sia_operaciones_alertas)
        Schema::table('car_sia_operaciones_alertas', function (Blueprint $table) {
            $table->index(['id_car_sia_operaciones', 'created_at'], 'idx_csoa_operacion_fecha');
            $table->index(['numero_bloque', 'id_car_sia_operaciones'], 'idx_csoa_bloque_operacion');
        });
    }

    /**
     * Revierte las migraciones (Borra los índices).
     */
    public function down()
    {
        Schema::table('car_sia_operaciones', function (Blueprint $table) {
            $table->dropIndex('idx_cso_blq_fecha');
            $table->dropIndex('idx_cso_radicado');
            $table->dropIndex('idx_cso_tercero');
        });

        Schema::table('car_sia_estados_operacion', function (Blueprint $table) {
            $table->dropIndex('idx_cseo_operacion_fecha');
            $table->dropIndex('idx_cseo_bloque_operacion');
        });

        Schema::table('car_sia_tipos_operacion', function (Blueprint $table) {
            $table->dropIndex('idx_csto_operacion_fecha');
        });

        Schema::table('car_sia_operaciones_alertas', function (Blueprint $table) {
            $table->dropIndex('idx_csoa_operacion_fecha');
            $table->dropIndex('idx_csoa_bloque_operacion');
        });
    }
};
