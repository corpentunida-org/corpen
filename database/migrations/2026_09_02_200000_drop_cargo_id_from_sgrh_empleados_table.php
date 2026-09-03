<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// El cargo también pasa a venir únicamente del contrato (mismo criterio que
// fecha_ingreso/salario_asignado en 2026_09_02_180000): Empleado::getCargoIdAttribute()
// lo deriva de contratoActivo->cargo_id. Confirmado antes de migrar: los 2 colaboradores
// reales ya tenían Empleado.cargo_id == su contrato activo.cargo_id, sin divergencia que
// reconciliar.
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('sgrh_empleados', function (Blueprint $table) {
            $table->dropForeign(['cargo_id']);
            $table->dropColumn('cargo_id');
        });
    }

    public function down(): void
    {
        Schema::table('sgrh_empleados', function (Blueprint $table) {
            $table->foreignId('cargo_id')->nullable()->after('cod_ter')->constrained('sgrh_cargos')->nullOnDelete();
        });
    }
};
