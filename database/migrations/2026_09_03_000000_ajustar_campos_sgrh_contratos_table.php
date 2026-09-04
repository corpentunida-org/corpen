<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

// Ajustes solicitados sobre sgrh_contratos:
// - fecha_creacion_contrato: fecha en que se redactó/suscribió el contrato, distinta de
//   fecha_inicio (cuándo empieza a regir) y de created_at (cuándo se guardó el registro en el
//   sistema, que puede ser semanas después si se digitó tarde). Para los contratos ya
//   existentes no hay forma de conocer la fecha real de creación del documento, así que se
//   backfillea con created_at como mejor aproximación disponible.
// - observaciones: se elimina del formulario de contrato (reemplazada por el historial
//   estructurado de causales/modificaciones) — antes de borrar la columna, cualquier texto que
//   tuviera se preserva como un evento más en sgrh_contrato_modificaciones, para no perder esa
//   trazabilidad.
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('sgrh_contratos', function (Blueprint $table) {
            $table->date('fecha_creacion_contrato')->nullable()->after('empleado_id');
        });

        foreach (DB::table('sgrh_contratos')->get() as $contrato) {
            DB::table('sgrh_contratos')->where('id', $contrato->id)->update([
                'fecha_creacion_contrato' => \Illuminate\Support\Carbon::parse($contrato->created_at)->format('Y-m-d'),
            ]);

            if (!empty($contrato->observaciones)) {
                DB::table('sgrh_contrato_modificaciones')->insert([
                    'contrato_id' => $contrato->id,
                    'causal' => 'Otra',
                    'observacion' => '[Migrado desde el campo "Observaciones" del contrato, retirado del formulario] ' . $contrato->observaciones,
                    'user_id' => null,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }

        Schema::table('sgrh_contratos', function (Blueprint $table) {
            $table->dropColumn('observaciones');
        });
    }

    public function down(): void
    {
        Schema::table('sgrh_contratos', function (Blueprint $table) {
            $table->dropColumn('fecha_creacion_contrato');
            $table->text('observaciones')->nullable();
        });
    }
};
