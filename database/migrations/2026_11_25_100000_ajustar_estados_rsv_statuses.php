<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * Catálogo de estados de reservas (rsv_statuses):
 *  · "No Show" pasa a llamarse "Incumplida" (el cliente nunca llegó y no avisó).
 *  · Se agrega "Re asignada".
 * Idempotente: no duplica ni pisa estados que ya existan.
 */
return new class extends Migration
{
    public function up(): void
    {
        DB::table('rsv_statuses')
            ->where('name', 'No Show')
            ->update(['name' => 'Incumplida', 'updated_at' => now()]);

        $existe = DB::table('rsv_statuses')
            ->whereRaw('LOWER(REPLACE(name, " ", "")) = ?', ['reasignada'])
            ->exists();

        if (!$existe) {
            DB::table('rsv_statuses')->insert([
                'name'       => 'Re asignada',
                'color_hex'  => '#6366f1',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }

    public function down(): void
    {
        DB::table('rsv_statuses')
            ->where('name', 'Incumplida')
            ->update(['name' => 'No Show', 'updated_at' => now()]);

        // "Re asignada" solo se borra si ninguna reserva la usa
        $id = DB::table('rsv_statuses')->where('name', 'Re asignada')->value('id');
        if ($id && !DB::table('rsv_reservas')->where('id_rsv_statuses', $id)->exists()) {
            DB::table('rsv_statuses')->where('id', $id)->delete();
        }
    }
};
