<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// Campos de contacto propios del CARGO (no de la persona que lo ocupa — esos ya viven en
// MaeTerceros). Replican la estructura de gdo_cargo para poder migrar sus datos reales
// (ver 2026_08_31_210002_migrate_gdo_area_cargo_data_to_sgrh).
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('sgrh_cargos', function (Blueprint $table) {
            $table->string('telefono_corporativo', 50)->nullable()->after('jornada');
            $table->string('celular_corporativo', 50)->nullable()->after('telefono_corporativo');
            $table->string('ext_corporativo', 20)->nullable()->after('celular_corporativo');
            $table->string('correo_corporativo')->nullable()->after('ext_corporativo');
            $table->string('gmail_corporativo')->nullable()->after('correo_corporativo');
            $table->string('manual_funciones')->nullable()->after('gmail_corporativo');
        });
    }

    public function down(): void
    {
        Schema::table('sgrh_cargos', function (Blueprint $table) {
            $table->dropColumn([
                'telefono_corporativo',
                'celular_corporativo',
                'ext_corporativo',
                'correo_corporativo',
                'gmail_corporativo',
                'manual_funciones',
            ]);
        });
    }
};
