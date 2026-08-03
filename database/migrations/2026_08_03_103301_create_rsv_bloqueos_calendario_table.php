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
        Schema::create('rsv_bloqueos_calendario', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_rsv_catalogo_inmueble')->constrained('rsv_catalogo_inmueble')->cascadeOnDelete();
            $table->date('fecha_inicio');
            $table->date('fecha_fin');
            $table->string('motivo', 255);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rsv_bloqueos_calendario');
    }
};
