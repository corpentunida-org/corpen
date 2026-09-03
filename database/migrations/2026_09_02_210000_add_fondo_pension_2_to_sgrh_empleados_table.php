<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// Segundo fondo de pensión: preparación para la reforma pensional (Ley 2381 de 2024), que
// introduce un sistema de pilares — un colaborador puede terminar cotizando a dos fondos
// simultáneamente (ej. Colpensiones para el pilar contributivo + un fondo privado para el
// complementario), no solo a uno como antes.
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('sgrh_empleados', function (Blueprint $table) {
            $table->string('fondo_pension_2')->nullable()->after('fondo_pension');
        });
    }

    public function down(): void
    {
        Schema::table('sgrh_empleados', function (Blueprint $table) {
            $table->dropColumn('fondo_pension_2');
        });
    }
};
