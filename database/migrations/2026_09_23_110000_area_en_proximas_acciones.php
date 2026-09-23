<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Mismo patrón que Tipos y Resultados de interacción: agrega area (nullable) a
 * int_next_actions. Las 6 acciones existentes (Ninguna, Llamada, Envío de propuesta, Reunión,
 * mensaje Whatsapp, Subir Acuerdo de Pago "PR") quedan con area=NULL — compartidas para todas
 * las áreas ("Todos"), mismo criterio que se usó para los catálogos anteriores.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('int_next_actions', function (Blueprint $table) {
            $table->string('area')->nullable()->after('name');
        });

        DB::table('int_next_actions')->update(['area' => null]);
    }

    public function down(): void
    {
        Schema::table('int_next_actions', function (Blueprint $table) {
            $table->dropColumn('area');
        });
    }
};
