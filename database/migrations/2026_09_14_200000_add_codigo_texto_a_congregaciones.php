<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * `codigo` es INT (no puede distinguir "0101" de "101" — MySQL las guarda como el mismo valor
 * 101), pero el listado fuente a veces trae el código como texto con ceros a la izquierda. En
 * los casos verificados esto era solo formato del sistema viejo (misma congregación, mismo
 * nombre/pastor/distrito), así que NO se cambia cómo se identifica la congregación — se guarda
 * el texto crudo del Excel aparte, solo para trazabilidad/auditoría.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('MaeCongregaciones', function (Blueprint $table) {
            $table->string('codigo_texto', 20)->nullable()->after('codigo');
        });
    }

    public function down(): void
    {
        Schema::table('MaeCongregaciones', function (Blueprint $table) {
            $table->dropColumn('codigo_texto');
        });
    }
};
