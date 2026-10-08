<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Habilita cre_legacy_solicitudes para recibir solicitudes NUEVAS (creadas desde la app), no
 * solo las importadas del archivo histórico — por eso "origen" para distinguir unas de otras.
 * Campos nuevos que pide el formulario físico y que no tenían casa en ningún lado:
 * tiene_credito_actual/cual_credito_actual, y la ruta del PDF/foto del formulario firmado.
 *
 * cre_cat_ingresos / cre_cat_egresos: catálogos de categorías de ingresos/egresos tal como
 * aparecen en el formulario físico. Los movimientos legacy usan cod_ing/cod_egre (códigos
 * "01"-"27" sin diccionario conocido); los movimientos nuevos usan cat_ingreso_id/cat_egreso_id
 * (FK a estos catálogos) en vez de intentar adivinar a qué código legacy corresponde cada
 * categoría del formulario.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('cre_legacy_solicitudes', function (Blueprint $table) {
            if (!Schema::hasColumn('cre_legacy_solicitudes', 'origen')) {
                $table->string('origen', 20)->default('legacy_import')->after('id');
            }
            if (!Schema::hasColumn('cre_legacy_solicitudes', 'tiene_credito_actual')) {
                $table->boolean('tiene_credito_actual')->nullable();
            }
            if (!Schema::hasColumn('cre_legacy_solicitudes', 'cual_credito_actual')) {
                $table->string('cual_credito_actual', 255)->nullable();
            }
            if (!Schema::hasColumn('cre_legacy_solicitudes', 'ruta_formulario_firmado')) {
                $table->string('ruta_formulario_firmado', 500)->nullable();
            }
        });

        if (!Schema::hasTable('cre_cat_ingresos')) {
            Schema::create('cre_cat_ingresos', function (Blueprint $table) {
                $table->id();
                $table->string('nombre', 100);
                $table->unsignedInteger('orden')->default(0);
            });
            DB::table('cre_cat_ingresos')->insert([
                ['nombre' => 'Diezmos Netos', 'orden' => 1],
                ['nombre' => 'Nivelación', 'orden' => 2],
                ['nombre' => 'Arriendos Recibidos', 'orden' => 3],
                ['nombre' => 'Pensión', 'orden' => 4],
                ['nombre' => 'Otros Ingresos', 'orden' => 5],
            ]);
        }

        if (!Schema::hasTable('cre_cat_egresos')) {
            Schema::create('cre_cat_egresos', function (Blueprint $table) {
                $table->id();
                $table->string('nombre', 100);
                $table->unsignedInteger('orden')->default(0);
            });
            DB::table('cre_cat_egresos')->insert([
                ['nombre' => 'Alimentación', 'orden' => 1],
                ['nombre' => 'Transporte', 'orden' => 2],
                ['nombre' => 'Educación', 'orden' => 3],
                ['nombre' => 'Arriendo', 'orden' => 4],
                ['nombre' => 'Servicios Públicos y/o Telefonía Celular', 'orden' => 5],
                ['nombre' => 'Cuota Préstamos Corpentunida', 'orden' => 6],
                ['nombre' => 'Cuota Bancos + Tarjetas de Crédito', 'orden' => 7],
                ['nombre' => 'Otros Préstamos', 'orden' => 8],
                ['nombre' => 'Otros Gastos', 'orden' => 9],
            ]);
        }

        Schema::table('cre_legacy_movimientos_ingresos', function (Blueprint $table) {
            if (!Schema::hasColumn('cre_legacy_movimientos_ingresos', 'cat_ingreso_id')) {
                $table->foreignId('cat_ingreso_id')->nullable()->after('cod_ing')->constrained('cre_cat_ingresos');
            }
        });

        Schema::table('cre_legacy_movimientos_gastos', function (Blueprint $table) {
            if (!Schema::hasColumn('cre_legacy_movimientos_gastos', 'cat_egreso_id')) {
                $table->foreignId('cat_egreso_id')->nullable()->after('cod_egre')->constrained('cre_cat_egresos');
            }
        });
    }

    public function down(): void
    {
        Schema::table('cre_legacy_movimientos_gastos', function (Blueprint $table) {
            if (Schema::hasColumn('cre_legacy_movimientos_gastos', 'cat_egreso_id')) {
                $table->dropConstrainedForeignId('cat_egreso_id');
            }
        });
        Schema::table('cre_legacy_movimientos_ingresos', function (Blueprint $table) {
            if (Schema::hasColumn('cre_legacy_movimientos_ingresos', 'cat_ingreso_id')) {
                $table->dropConstrainedForeignId('cat_ingreso_id');
            }
        });
        Schema::dropIfExists('cre_cat_egresos');
        Schema::dropIfExists('cre_cat_ingresos');

        Schema::table('cre_legacy_solicitudes', function (Blueprint $table) {
            foreach (['origen', 'tiene_credito_actual', 'cual_credito_actual', 'ruta_formulario_firmado'] as $c) {
                if (Schema::hasColumn('cre_legacy_solicitudes', $c)) {
                    $table->dropColumn($c);
                }
            }
        });
    }
};
