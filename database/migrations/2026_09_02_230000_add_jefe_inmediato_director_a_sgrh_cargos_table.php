<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// Jerarquía de aprobación: cada cargo puede tener un "jefe inmediato" (el cargo al que reporta
// directamente) y un "director" (nivel superior, ej. dirección del área) — ambos son
// referencias a otro cargo del mismo catálogo. Preparación para el motor de aprobaciones de
// permisos/vacaciones de un bloque posterior (Bloque C, ver plan de SGRH): cuando alguien pida
// un permiso, se podrá enrutar la aprobación siguiendo esta cadena a partir de su cargo.
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('sgrh_cargos', function (Blueprint $table) {
            $table->foreignId('jefe_inmediato_id')->nullable()->after('sgrh_area_id')
                ->constrained('sgrh_cargos')->nullOnDelete();
            $table->foreignId('director_id')->nullable()->after('jefe_inmediato_id')
                ->constrained('sgrh_cargos')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('sgrh_cargos', function (Blueprint $table) {
            $table->dropForeign(['jefe_inmediato_id']);
            $table->dropForeign(['director_id']);
            $table->dropColumn(['jefe_inmediato_id', 'director_id']);
        });
    }
};
