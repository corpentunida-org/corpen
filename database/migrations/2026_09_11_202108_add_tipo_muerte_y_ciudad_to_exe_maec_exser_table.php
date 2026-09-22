<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// `ciudad_fallecimiento_id` referencia geo_ciudades.id_ciudad (mismo catálogo que ya usa
// asociados/edit.blade.php para "Ciudad del Distrito") pero, igual que `municipio` en esta misma
// tabla, sin constraint -> foreign(): esta tabla ya guarda ubicaciones sueltas sin FK real.
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('EXE_MAEC_EXSER', function (Blueprint $table) {
            $table->string('tipoMuerte', 20)->nullable()->after('lugarFallecimiento');
            $table->unsignedBigInteger('ciudad_fallecimiento_id')->nullable()->after('tipoMuerte');
        });
    }

    public function down(): void
    {
        Schema::table('EXE_MAEC_EXSER', function (Blueprint $table) {
            $table->dropColumn(['tipoMuerte', 'ciudad_fallecimiento_id']);
        });
    }
};
