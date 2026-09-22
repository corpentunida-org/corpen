<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Algunas congregaciones están fuera de Colombia (ej. Lima - Perú) y no tienen cómo
 * representarse en el catálogo de municipios (que solo cubre DIVIPOLA colombiano). Se agrega
 * un departamento y municipio "sentinela" para elegir como "Otro / Exterior" en el formulario,
 * más un campo de texto libre para guardar el detalle (ciudad/país) que se pierde si solo se
 * elige el sentinela.
 *
 * Los ids (999 / 9999) están muy por encima del rango real (departamentos hasta 99, municipios
 * hasta 1125) para que nunca choquen con un código DIVIPOLA real.
 */
return new class extends Migration
{
    public function up(): void
    {
        DB::table('MaeDepartamentos')->updateOrInsert(
            ['codigo_Dane' => 999],
            ['nombre' => 'Exterior']
        );

        DB::table('MaeMunicipios')->updateOrInsert(
            ['id' => 9999],
            ['codigo_Dane' => 999999, 'nombre' => 'Otro / Exterior', 'id_departamento' => 999]
        );

        Schema::table('MaeCongregaciones', function (Blueprint $table) {
            $table->string('municipio_exterior_detalle', 150)->nullable()->after('municipio');
        });
    }

    public function down(): void
    {
        Schema::table('MaeCongregaciones', function (Blueprint $table) {
            $table->dropColumn('municipio_exterior_detalle');
        });

        DB::table('MaeMunicipios')->where('id', 9999)->delete();
        DB::table('MaeDepartamentos')->where('codigo_Dane', 999)->delete();
    }
};
