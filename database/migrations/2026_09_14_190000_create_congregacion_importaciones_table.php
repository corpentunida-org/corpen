<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Bitácora de cada carga del listado de congregaciones (se repite ~mensual) — quién la corrió,
 * cuándo, con qué archivo, y el resumen de qué cambió, para poder auditar después "por qué
 * cambió el pastor de X congregación" sin tener que adivinar.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('congregacion_importaciones', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users');
            $table->string('archivo_nombre');
            $table->json('resumen');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('congregacion_importaciones');
    }
};
