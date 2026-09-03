<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// Salario realmente asignado al colaborador (llenado manual) — distinto del salario_base del
// cargo (sgrh_cargos), que es el de referencia del cargo en general, no el pactado con la
// persona (ej. por experiencia, negociación, etc.).
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('sgrh_empleados', function (Blueprint $table) {
            $table->decimal('salario_asignado', 12, 2)->nullable()->after('cargo_id');
        });
    }

    public function down(): void
    {
        Schema::table('sgrh_empleados', function (Blueprint $table) {
            $table->dropColumn('salario_asignado');
        });
    }
};
