<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('car_sia_config', function (Blueprint $table) {
            // Agrega el campo booleano con valor por defecto true
            // after() lo ubica visualmente después de frecuencia_recordatorio_dias en la BD
            $table->boolean('estado_activo')->default(true)->after('frecuencia_recordatorio_dias');
        });
    }

    public function down(): void
    {
        Schema::table('car_sia_config', function (Blueprint $table) {
            $table->dropColumn('estado_activo');
        });
    }
};
