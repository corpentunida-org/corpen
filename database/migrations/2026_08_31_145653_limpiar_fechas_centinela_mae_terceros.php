<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * Limpieza de datos (no de esquema): '1899-12-30' y '1900-01-01' en fec_nac/fec_falle de
 * MaeTerceros son fechas "centinela" heredadas de una importación vieja (Excel trata una
 * celda de fecha vacía como el día 0), no fechas reales. Verificado antes de correr esto:
 * ningún otro módulo del código depende de que estas columnas tengan un valor no-nulo.
 *
 * down() no revierte: una vez puesto NULL no hay forma de recuperar el valor centinela
 * original (tampoco tendría sentido hacerlo, era basura).
 */
return new class extends Migration
{
    public function up(): void
    {
        $fechasCentinela = ['1899-12-30 00:00:00', '1900-01-01 00:00:00'];

        DB::table('MaeTerceros')
            ->whereIn('fec_nac', $fechasCentinela)
            ->update(['fec_nac' => null]);

        DB::table('MaeTerceros')
            ->whereIn('fec_falle', $fechasCentinela)
            ->update(['fec_falle' => null]);
    }

    public function down(): void
    {
        // Intencionalmente no reversible — ver comentario de clase.
    }
};
