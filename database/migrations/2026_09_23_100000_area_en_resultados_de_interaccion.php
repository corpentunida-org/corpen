<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Mismo patrón que 2026_09_22_120000_area_en_tipos_de_interaccion: agrega area (nullable) a
 * int_outcomes para poder filtrar/administrar por área, igual que ya se hizo con los Tipos. Los
 * 3 resultados existentes (Efectivo, No efectivo, Reasignado a otro Operador) quedan con
 * area=NULL — "Todos" pidió el usuario para los ya existentes, mismo significado que "Compartido"
 * en Tipos: visibles y usables desde cualquier área, no exclusivos de una.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('int_outcomes', function (Blueprint $table) {
            $table->string('area')->nullable()->after('estado');
        });

        // Explícito aunque ya nacen NULL por defecto: deja constancia de la intención (compartidos
        // para todas las áreas), no que se les olvidó asignar una.
        DB::table('int_outcomes')->update(['area' => null]);
    }

    public function down(): void
    {
        Schema::table('int_outcomes', function (Blueprint $table) {
            $table->dropColumn('area');
        });
    }
};
