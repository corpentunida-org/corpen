<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Dos cosas que faltaban para modelar las reglas reales de las líneas de crédito
 * (ver tablas de "Líneas de Créditos y sus Generalidades" que compartió el usuario):
 *
 * 1. tasa_interes_alt / edad_desde_tasa_alt: algunas líneas tienen una tasa reducida a partir
 *    de cierta edad del pastor (ej: RapiCrédito Libre Inversión: 1% normal, 0.9% desde los 55
 *    años), sin ser una línea distinta (a diferencia de "Libre Inversión Menores/Mayores", que
 *    sí son dos líneas separadas). Nullable: solo aplica a la línea que lo necesite.
 *
 * 2. cre_lineas_creditos_documentos: lista de documentos necesarios por línea. No existía
 *    ninguna tabla para esto (cre_tipo_documentos existe pero está vacía y amarrada a
 *    cre_etapas_id, un concepto distinto — documentos por etapa del proceso, no por línea).
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('cre_lineas_creditos', function (Blueprint $table) {
            if (!Schema::hasColumn('cre_lineas_creditos', 'tasa_interes_alt')) {
                $table->decimal('tasa_interes_alt', 8, 2)->nullable()->after('tasa_interes');
            }
            if (!Schema::hasColumn('cre_lineas_creditos', 'edad_desde_tasa_alt')) {
                $table->unsignedTinyInteger('edad_desde_tasa_alt')->nullable()->after('tasa_interes_alt');
            }
        });

        if (!Schema::hasTable('cre_lineas_creditos_documentos')) {
            Schema::create('cre_lineas_creditos_documentos', function (Blueprint $table) {
                $table->id();
                $table->foreignId('cre_lineas_creditos_id')->constrained('cre_lineas_creditos')->cascadeOnDelete();
                $table->string('descripcion', 500);
                $table->unsignedInteger('orden')->default(0);
                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('cre_lineas_creditos_documentos');
        Schema::table('cre_lineas_creditos', function (Blueprint $table) {
            if (Schema::hasColumn('cre_lineas_creditos', 'tasa_interes_alt')) {
                $table->dropColumn('tasa_interes_alt');
            }
            if (Schema::hasColumn('cre_lineas_creditos', 'edad_desde_tasa_alt')) {
                $table->dropColumn('edad_desde_tasa_alt');
            }
        });
    }
};
