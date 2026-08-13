<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('log_integraciones', function (Blueprint $table) {
            $table->id();

            // Datos generales de la petición
            $table->string('nombre_api'); // Ej: 'API Pastors'
            $table->string('endpoint'); // Ej: 'https://.../api/Pastors'
            $table->string('metodo')->default('GET'); // GET, POST, PUT, etc.

            // Resultados de la petición
            $table->integer('codigo_respuesta')->nullable(); // 200, 401, 500, etc.
            $table->integer('tiempo_respuesta_ms')->nullable(); // Para medir rendimiento
            $table->string('estado'); // Ej: 'Exitoso', 'Error', 'Timeout'

            // Detalles del error (si ocurre)
            $table->text('mensaje_error')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('log_integraciones');
    }
};
