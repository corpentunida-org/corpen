<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Soporte del Portal Asociado:
 *  - rsv_mensajes.id_scp_soporte: enlaza la nota del chat de la reserva con el ticket creado en
 *    la plataforma Soportes (scp_soportes), para mostrarle al asociado su número y estado.
 *  - Cargo genérico "Asociado (portal de reservas)" en gdo_cargo: scp_soportes.id_gdo_cargo es
 *    NOT NULL y los asociados no tienen cargo; los tickets que crean desde una reserva usan este.
 */
return new class extends Migration
{
    public const CARGO_ASOCIADO = 'Asociado (portal de reservas)';

    public function up(): void
    {
        if (!Schema::hasColumn('rsv_mensajes', 'id_scp_soporte')) {
            Schema::table('rsv_mensajes', function (Blueprint $table) {
                $table->unsignedBigInteger('id_scp_soporte')->nullable()->after('url_archivo')->index();
            });
        }

        if (!DB::table('gdo_cargo')->where('nombre_cargo', self::CARGO_ASOCIADO)->exists()) {
            DB::table('gdo_cargo')->insert([
                'nombre_cargo' => self::CARGO_ASOCIADO,
                'estado'       => 1,
                'observacion'  => 'Cargo genérico para tickets creados por asociados desde el Portal Asociado (RSV). No asignar a empleados.',
                'created_at'   => now(),
                'updated_at'   => now(),
            ]);
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('rsv_mensajes', 'id_scp_soporte')) {
            Schema::table('rsv_mensajes', function (Blueprint $table) {
                $table->dropIndex(['id_scp_soporte']);
                $table->dropColumn('id_scp_soporte');
            });
        }

        // Solo se borra si ningún ticket lo usa (la FK de scp_soportes es ON DELETE CASCADE).
        $cargoId = DB::table('gdo_cargo')->where('nombre_cargo', self::CARGO_ASOCIADO)->value('id');
        if ($cargoId && !DB::table('scp_soportes')->where('id_gdo_cargo', $cargoId)->exists()) {
            DB::table('gdo_cargo')->where('id', $cargoId)->delete();
        }
    }
};
