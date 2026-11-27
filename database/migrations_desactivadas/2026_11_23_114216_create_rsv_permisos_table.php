<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('rsv_permisos', function (Blueprint $table) {
            $table->id();
            $table->string('codigo', 120)->unique(); // Ej: rsv.reservas.ver
            $table->string('nombre', 120);
            $table->string('descripcion', 255)->nullable();
            $table->string('modulo', 80); // Ej: admin_reservas, cliente_pagos
            $table->boolean('estado')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('rsv_permisos');
    }
};
