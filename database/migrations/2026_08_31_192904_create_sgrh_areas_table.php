<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// Catálogo propio de SGRH — no es gdo_area del módulo Archivo/"Gestión" (ver
// 2026_08_31_210002_migrate_gdo_area_cargo_data_to_sgrh, que copia esos datos aquí una vez).
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sgrh_areas', function (Blueprint $table) {
            $table->id();
            $table->string('nombre')->unique();
            $table->text('descripcion')->nullable();
            $table->boolean('activo')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sgrh_areas');
    }
};
