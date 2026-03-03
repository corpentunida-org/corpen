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
        Schema::create('inv_referencias', function (Blueprint $table) {
            $table->id(); // Crea automáticamente el 'id' como int, PK y autoincremental
            $table->string('nombre');
            $table->string('detalle'); // Lo dejé en minúscula por convención, pero puedes poner 'Detalle' si lo prefieres
            $table->timestamps(); // Agrega created_at y updated_at (recomendado)
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('inv_referencias');
    }
};