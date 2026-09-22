<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * El listado de pastores trae tipo de sangre y MaeTerceros no tenía dónde guardarlo. Mismo
 * nombre de columna que ya usa Sgrh\Empleado para el mismo dato, por consistencia.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('MaeTerceros', function (Blueprint $table) {
            $table->string('tipo_sangre', 5)->nullable()->after('sexo');
        });
    }

    public function down(): void
    {
        Schema::table('MaeTerceros', function (Blueprint $table) {
            $table->dropColumn('tipo_sangre');
        });
    }
};
