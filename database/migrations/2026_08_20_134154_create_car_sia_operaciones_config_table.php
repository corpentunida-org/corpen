<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('car_sia_operaciones_config', function (Blueprint $table) {
            $table->id(); // BIGINT UNSIGNED, PK, NOT NULL

            // Ajustado a string (VARCHAR) de 50 e indexado para soportar los bloques con texto
            $table->string('numero_bloque', 50)->index();

            // Llave foránea a operaciones (nullable porque puede operar según el bloque padre)
            $table->foreignId('id_car_sia_operaciones')
                  ->nullable()
                  ->constrained('car_sia_operaciones');

            // Llave foránea a la configuración (not null)
            $table->foreignId('id_car_sia_config')
                  ->constrained('car_sia_config');

            // Estado de la notificación
            $table->boolean('estado_notificacion')->default(true);

            $table->timestamps(); // created_at y updated_at (TIMESTAMP NULL)
            $table->softDeletes(); // deleted_at (TIMESTAMP NULL)
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('car_sia_operaciones_config');
    }
};
