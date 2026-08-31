<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * Continuación de 2026_08_31_145653_limpiar_fechas_centinela_mae_terceros: el mismo problema
 * de fechas "centinela" ('1899-12-30' / '1900-01-01') afecta también fec_expcc (fecha de
 * expedición de cédula) — 14.956 de 21.101 filas no nulas (~71%).
 */
return new class extends Migration
{
    public function up(): void
    {
        DB::table('MaeTerceros')
            ->whereIn('fec_expcc', ['1899-12-30 00:00:00', '1900-01-01 00:00:00'])
            ->update(['fec_expcc' => null]);
    }

    public function down(): void
    {
        // Intencionalmente no reversible — ver comentario de clase.
    }
};
