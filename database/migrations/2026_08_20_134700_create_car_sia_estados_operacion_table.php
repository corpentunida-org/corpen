<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('car_sia_estados_operacion', function (Blueprint $table) {
            $table->id(); // BIGINT UNSIGNED, PK, AUTO_INCREMENT

            // Llave foránea a operaciones (nullable como indica el diseño)
            $table->foreignId('id_car_sia_operaciones')
                  ->nullable()
                  ->constrained('car_sia_operaciones');

            // Identificador del bloque heredado de operaciones
            $table->string('numero_bloque', 50)->index();

            // Llave foránea a los estados (NOT NULL)
            $table->foreignId('id_car_sia_estados')
                  ->constrained('car_sia_estados');

            $table->timestamps(); // created_at y updated_at (TIMESTAMP NULL)
            $table->softDeletes(); // deleted_at (TIMESTAMP NULL)
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('car_sia_estados_operacion');
    }
};
