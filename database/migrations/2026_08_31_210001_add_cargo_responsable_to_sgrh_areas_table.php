<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// Cargo que es "jefe" de esta área — replica gdo_area.GDO_cargo_id / GdoArea::jefeCargo(),
// para poder migrar esa relación (ver 2026_08_31_210002_migrate_gdo_area_cargo_data_to_sgrh).
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('sgrh_areas', function (Blueprint $table) {
            $table->foreignId('cargo_responsable_id')->nullable()->after('descripcion')
                ->constrained('sgrh_cargos')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('sgrh_areas', function (Blueprint $table) {
            $table->dropForeign(['cargo_responsable_id']);
            $table->dropColumn('cargo_responsable_id');
        });
    }
};
