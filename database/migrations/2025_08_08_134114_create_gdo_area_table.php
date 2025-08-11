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
        Schema::create('gdo_area', function (Blueprint $table) {
            $table->id();

            $table->string('nombre', 100); // Nombre del área
            $table->text('descripcion')->nullable(); // Descripción opcional
            $table->enum('estado', ['activo', 'inactivo'])->default('activo'); // Estado del área

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('gdo_area');
    }
};
