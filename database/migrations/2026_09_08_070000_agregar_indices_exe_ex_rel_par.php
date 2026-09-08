<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * EXE_ExRelPar (beneficiarios de Exequiales, ~29.000 filas) no tenía índice en 'cedula' ni en
 * 'cod_cli' — su PRIMARY real es 'idrow' (autoincremental), pero casi toda la app filtra por
 * 'cedula' (actualizar/eliminar beneficiario, marcar fallecido) y 'cod_cli' (relación con el
 * titular). Sin índice, cada una de esas consultas hacía un full table scan (~700ms medidos en
 * vivo vs. unos pocos ms con índice) — la causa más probable de la lentitud reportada en el
 * módulo. 'cedula' NO es único en los datos actuales (263 valores duplicados), así que el
 * índice es simple, no UNIQUE.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('EXE_ExRelPar', function (Blueprint $table) {
            $table->index('cedula', 'idx_exerelpar_cedula');
            $table->index('cod_cli', 'idx_exerelpar_cod_cli');
        });
    }

    public function down(): void
    {
        Schema::table('EXE_ExRelPar', function (Blueprint $table) {
            $table->dropIndex('idx_exerelpar_cedula');
            $table->dropIndex('idx_exerelpar_cod_cli');
        });
    }
};
