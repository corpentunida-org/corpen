<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('sgrh_empleados', function (Blueprint $table) {
            // nullOnDelete (no cascade): si se borra el cargo, el empleado no debe borrarse
            // con él — solo queda sin cargo asignado.
            $table->foreignId('cargo_id')->nullable()->after('cod_ter')->constrained('sgrh_cargos')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('sgrh_empleados', function (Blueprint $table) {
            $table->dropConstrainedForeignId('cargo_id');
        });
    }
};
