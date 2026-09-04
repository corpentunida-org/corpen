<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

// Cada evento del historial (creación + modificaciones) guarda ahora una "foto" de cómo
// quedó el contrato en ese momento (tipo, cargo, fechas, salario, estado) — es lo que permite
// ver/imprimir el contrato tal como estaba vigente en cualquier punto de su historia, no solo
// el estado actual. 'Creación' pasa a ser un evento real en la tabla (antes era puramente
// visual, calculado desde created_at) para poder colgarle su propio snapshot.
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('sgrh_contrato_modificaciones', function (Blueprint $table) {
            $table->json('snapshot')->nullable()->after('observacion');
        });

        // Backfill: a cada contrato existente se le crea su evento de Creación con los datos
        // ACTUALES como mejor aproximación disponible (esta función no existía cuando se creó
        // el registro, así que no hay forma de recuperar los valores originales exactos si el
        // contrato ya fue modificado desde entonces).
        foreach (DB::table('sgrh_contratos')->get() as $contrato) {
            $tieneModificaciones = DB::table('sgrh_contrato_modificaciones')
                ->where('contrato_id', $contrato->id)
                ->exists();

            $tipoContrato = DB::table('sgrh_tipos_contrato')->where('id', $contrato->tipo_contrato_id)->value('nombre');
            $cargo = DB::table('sgrh_cargos')->where('id', $contrato->cargo_id)->first();
            $area = $cargo && $cargo->sgrh_area_id ? DB::table('sgrh_areas')->where('id', $cargo->sgrh_area_id)->value('nombre') : null;

            $snapshot = json_encode([
                'tipo_contrato' => $tipoContrato,
                'cargo' => $cargo->nombre ?? null,
                'area' => $area,
                'fecha_creacion_contrato' => $contrato->fecha_creacion_contrato,
                'fecha_inicio' => $contrato->fecha_inicio,
                'fecha_vencimiento' => $contrato->fecha_vencimiento,
                'estado' => $contrato->estado,
                'salario_contrato' => $contrato->salario_contrato,
                'documento_url' => $contrato->documento_url,
            ]);

            DB::table('sgrh_contrato_modificaciones')->insert([
                'contrato_id' => $contrato->id,
                'causal' => 'Creación',
                'observacion' => $tieneModificaciones
                    ? 'Evento generado retroactivamente al implementar esta función — los datos mostrados son los más antiguos disponibles, no necesariamente los de la creación original del contrato.'
                    : null,
                'snapshot' => $snapshot,
                'user_id' => null,
                'created_at' => $contrato->created_at,
                'updated_at' => now(),
            ]);
        }
    }

    public function down(): void
    {
        DB::table('sgrh_contrato_modificaciones')->where('causal', 'Creación')->delete();

        Schema::table('sgrh_contrato_modificaciones', function (Blueprint $table) {
            $table->dropColumn('snapshot');
        });
    }
};
