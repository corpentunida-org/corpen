<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('car_sia_config', function (Blueprint $table) {
            $table->id(); // BIGINT UNSIGNED, PK, NOT NULL

            // Llave foránea hacia la tabla de acciones de vencimiento
            $table->foreignId('id_car_sia_acciones_vencimiento')
                  ->constrained('car_sia_acciones_vencimiento');

            $table->jsonb('parametros')->nullable(); // JSON / JSONB, NULL
            $table->unsignedInteger('frecuencia_recordatorio_dias')->nullable(); // INT UNSIGNED, NULL

            $table->timestamps(); // created_at y updated_at (TIMESTAMP NULL)
            $table->softDeletes(); // deleted_at (TIMESTAMP NULL)
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('car_sia_config');
    }
};
