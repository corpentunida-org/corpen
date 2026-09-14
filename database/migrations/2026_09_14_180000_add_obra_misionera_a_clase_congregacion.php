<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * Nueva clase para congregaciones de obra misionera en el exterior (ej. Lima - Perú), mismo
 * patrón que la ya existente "OBRA CARCELARIA" para un tipo especial de congregación.
 */
return new class extends Migration
{
    public function up(): void
    {
        DB::table('MaeClaseCongregacion')->updateOrInsert(
            ['id' => 8],
            ['nombre' => 'OBRA MISIONERA']
        );
    }

    public function down(): void
    {
        DB::table('MaeClaseCongregacion')->where('id', 8)->delete();
    }
};
