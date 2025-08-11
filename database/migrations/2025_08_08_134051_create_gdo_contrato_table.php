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
        Schema::create('gdo_contrato', function (Blueprint $table) {
            $table->id();

            $table->string('nombre', 150); // Nombre del contrato
            $table->date('fecha_inicio'); // Fecha de inicio
            $table->date('fecha_fin')->nullable(); // Fecha de finalización (nullable por si no tiene)
            $table->enum('estado', ['activo', 'inactivo'])->default('activo'); // Estado del contrato
            $table->text('observacion')->nullable(); // Observaciones adicionales
            $table->text('descripcion')->nullable(); // Descripción general
            $table->string('documento', 255)->nullable(); // Ruta o nombre del archivo del contrato

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('gdo_contrato');
    }
};
