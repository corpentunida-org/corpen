<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * "Tipo de interacción" (por qué llama/lo llaman) era un catálogo único y compartido, 100%
 * pensado para Cartera/Cobranza (Acuerdo Pago, Cobro, Paz y Salvo...) — inservible para Seguros
 * de Vida, que ya empezó a usar Daytrack. Cada área necesita los suyos.
 *
 * area = null: tipo compartido por cualquier área (ej. "Atención General"). area = 'cartera' /
 * 'seguros' / ...: solo esa área lo ve y lo usa (coincide con roles.area de la Matriz).
 *
 * "Atención General", "Seguimiento", "Estado Cuenta" y "Soporte de pago" ya existían y Seguros
 * pidió exactamente los mismos conceptos — se dejan compartidos (area=null) en vez de duplicarlos
 * con otro id para cada área. El resto del catálogo actual es específico de Cartera. Los nuevos
 * tipos de Seguros de Vida quedan area='seguros'.
 */
return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasColumn('int_types', 'area')) {
            Schema::table('int_types', function (Blueprint $table) {
                $table->string('area', 60)->nullable()->after('name');
            });
        }

        $compartidos = ['Atención General', 'Seguimiento', 'Estado Cuenta', 'Soporte de pago'];
        DB::table('int_types')->whereIn('name', $compartidos)->update(['area' => null]);
        DB::table('int_types')->whereNotIn('name', $compartidos)->whereNull('area')->update(['area' => 'cartera']);

        $ahora = now();
        $seguros = [
            'Coberturas', 'Afiliación y planes', 'Reclamaciones', 'Novedades',
            'Avalúos', 'Póliza Vida Deudor', 'Póliza Hogar', 'Informes',
        ];
        foreach ($seguros as $nombre) {
            DB::table('int_types')->insertOrIgnore([
                'name' => $nombre, 'area' => 'seguros', 'created_at' => $ahora, 'updated_at' => $ahora,
            ]);
        }
    }

    public function down(): void
    {
        Schema::table('int_types', function (Blueprint $table) {
            $table->dropColumn('area');
        });
    }
};
