<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('corr_correspondencia', function (Blueprint $table) {

            $table->string('id_radicado', 20)->primary();

            $table->dateTime('fecha_solicitud');
            $table->text('asunto');

            $table->boolean('es_confidencial')->default(false);

            $table->enum('medio_recibido', [
                'whatsapp',
                'email',
                'fisico',
                'llamada',
                'correspondencia'
            ]);

            // 👉 MISMO tipo que MaeTerceros.cod_ter
            $table->bigInteger('remitente_id');

            // 👉 Estas tablas son Laravel → unsigned
            $table->unsignedBigInteger('trd_id');
            $table->unsignedBigInteger('flujo_id');
            $table->unsignedBigInteger('estado_id');
            $table->unsignedBigInteger('usuario_id');

            $table->text('observacion_previa')->nullable();
            $table->boolean('finalizado')->default(false);
            $table->string('documento_arc')->nullable();

            $table->timestamps();
            $table->index('fecha_solicitud');

            // ===== FOREIGN KEYS =====

            $table->foreign('remitente_id')
                ->references('cod_ter')
                ->on('MaeTerceros');

            $table->foreign('trd_id')
                ->references('id_trd')
                ->on('corr_trd');

            $table->foreign('flujo_id')
                ->references('id')
                ->on('corr_flujo_de_trabajo');

            $table->foreign('estado_id')
                ->references('id')
                ->on('corr_estados');

            $table->foreign('usuario_id')
                ->references('id')
                ->on('users');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('corr_correspondencia');
    }
};
