<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

// El contacto corporativo (teléfono/celular/ext/correo/gmail) pasa de sgrh_cargos a
// sgrh_empleados: un cargo puede tener varias personas (a diferencia del gdo_cargo legado,
// 1 cargo = 1 persona), así que el contacto no puede vivir en el cargo compartido, tiene que
// ser por colaborador. salario_base se elimina sin reemplazo (el salario real ya vive en
// Contrato.salario_contrato desde la fase anterior).
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('sgrh_empleados', function (Blueprint $table) {
            $table->string('telefono_corporativo', 50)->nullable()->after('cargo_id');
            $table->string('celular_corporativo', 50)->nullable()->after('telefono_corporativo');
            $table->string('ext_corporativo', 20)->nullable()->after('celular_corporativo');
            $table->string('correo_corporativo')->nullable()->after('ext_corporativo');
            $table->string('gmail_corporativo')->nullable()->after('correo_corporativo');
        });

        // Preserva el contacto ya cargado: todo colaborador con cargo asignado hereda el
        // contacto que tenía ese cargo antes de que la columna se elimine de sgrh_cargos.
        DB::table('sgrh_empleados as e')
            ->join('sgrh_cargos as c', 'c.id', '=', 'e.cargo_id')
            ->update([
                'e.telefono_corporativo' => DB::raw('c.telefono_corporativo'),
                'e.celular_corporativo' => DB::raw('c.celular_corporativo'),
                'e.ext_corporativo' => DB::raw('c.ext_corporativo'),
                'e.correo_corporativo' => DB::raw('c.correo_corporativo'),
                'e.gmail_corporativo' => DB::raw('c.gmail_corporativo'),
            ]);

        Schema::table('sgrh_cargos', function (Blueprint $table) {
            $table->dropColumn([
                'salario_base',
                'telefono_corporativo',
                'celular_corporativo',
                'ext_corporativo',
                'correo_corporativo',
                'gmail_corporativo',
            ]);
        });
    }

    public function down(): void
    {
        Schema::table('sgrh_cargos', function (Blueprint $table) {
            $table->decimal('salario_base', 12, 2)->nullable()->after('sgrh_area_id');
            $table->string('telefono_corporativo', 50)->nullable()->after('jornada');
            $table->string('celular_corporativo', 50)->nullable()->after('telefono_corporativo');
            $table->string('ext_corporativo', 20)->nullable()->after('celular_corporativo');
            $table->string('correo_corporativo')->nullable()->after('ext_corporativo');
            $table->string('gmail_corporativo')->nullable()->after('correo_corporativo');
        });

        Schema::table('sgrh_empleados', function (Blueprint $table) {
            $table->dropColumn([
                'telefono_corporativo',
                'celular_corporativo',
                'ext_corporativo',
                'correo_corporativo',
                'gmail_corporativo',
            ]);
        });
    }
};
