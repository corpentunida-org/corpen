<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Campos de la estructura real de Distritos (formulario legado que se está migrando): cada
 * distrito tiene, además de Detalle/Compuesto, una cédula por cada cargo de la junta del
 * presbiterio. Se guardan como unsignedBigInteger para ser consistentes con MaeTerceros.cod_ter
 * (bigint) — MaeCongregaciones.pastor usa int y es más angosto de lo debido, no se repite ese
 * error aquí.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('MaeDistritos', function (Blueprint $table) {
            $table->unsignedBigInteger('cc_supervisor')->nullable()->after('COMPUEST');
            $table->unsignedBigInteger('cc_primer_presb')->nullable()->after('cc_supervisor');
            $table->unsignedBigInteger('cc_segundo_presb')->nullable()->after('cc_primer_presb');
            $table->unsignedBigInteger('cc_tercer_presb')->nullable()->after('cc_segundo_presb');
            $table->unsignedBigInteger('cc_secre_presb')->nullable()->after('cc_tercer_presb');
            $table->unsignedBigInteger('cc_teso_presb')->nullable()->after('cc_secre_presb');
            $table->unsignedBigInteger('cc_fiscal')->nullable()->after('cc_teso_presb');
            $table->unsignedBigInteger('cc_asesor_corpen')->nullable()->after('cc_fiscal');
        });
    }

    public function down(): void
    {
        Schema::table('MaeDistritos', function (Blueprint $table) {
            $table->dropColumn([
                'cc_supervisor',
                'cc_primer_presb',
                'cc_segundo_presb',
                'cc_tercer_presb',
                'cc_secre_presb',
                'cc_teso_presb',
                'cc_fiscal',
                'cc_asesor_corpen',
            ]);
        });
    }
};
