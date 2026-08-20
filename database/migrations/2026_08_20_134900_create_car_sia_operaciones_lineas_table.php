<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('car_sia_operaciones_lineas', function (Blueprint $table) {
            $table->id(); // BIGINT UNSIGNED, PK, AUTO_INCREMENT

            // Llave foránea a la operación
            $table->foreignId('id_car_sia_operaciones')->constrained('car_sia_operaciones');

            // Relación con tabla maestra del sistema (cre_lineas_creditos)
            $table->unsignedBigInteger('id_cre_lineas_creditos');
            $table->foreign('id_cre_lineas_creditos')
                  ->references('id')
                  ->on('cre_lineas_creditos');

            // Identificador de bloque heredado
            $table->string('numero_bloque', 50)->index();

            // Campos de detalle y configuración
            $table->text('observacion')->nullable();
            $table->string('calificacion')->nullable();
            $table->dateTime('fecha_venci')->nullable(); // DATE / DATETIME

            // Llave foránea al estado actual de la operación
            $table->foreignId('id_car_sia_estados_operacion')->constrained('car_sia_estados_operacion');

            // Campos de control de tiempos y automatización
            $table->timestamp('fecha_ultimo_recordatorio')->nullable();
            $table->integer('dias_mora_automaticos')->nullable();
            $table->timestamp('procesado_en')->nullable();

            $table->timestamps(); // created_at y updated_at
            $table->softDeletes(); // deleted_at
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('car_sia_operaciones_lineas');
    }
};
