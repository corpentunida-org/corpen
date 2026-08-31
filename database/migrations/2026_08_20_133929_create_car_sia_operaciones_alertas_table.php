<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('car_sia_operaciones_alertas', function (Blueprint $table) {
            // Según el documento, el ID es un VARCHAR de 50.
            // Lo defino como primary para mantener la integridad.
            $table->string('id', 50)->primary();

            // Llave foránea hacia los tipos de alerta
            $table->foreignId('id_car_sia_tipos_alerta')->constrained('car_sia_tipos_alerta');

            // Ajustado a string para soportar formato "API-2026-001"
            $table->string('numero_bloque', 50)->index();

            // Llave foránea a operaciones (nullable como indica el diseño)
            $table->foreignId('id_car_sia_operaciones')->nullable()->constrained('car_sia_operaciones');

            // Fechas de control
            $table->timestamp('fecha_programada')->nullable();
            $table->timestamp('procesado_en')->nullable(); // NULL indica que aún no ha sido procesada

            $table->timestamps(); // created_at y updated_at
            $table->softDeletes(); // deleted_at
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('car_sia_operaciones_alertas');
    }
};
