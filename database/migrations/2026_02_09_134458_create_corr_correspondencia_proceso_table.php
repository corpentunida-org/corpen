<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('corr_correspondencia_proceso', function (Blueprint $table) {

            // PK
            $table->increments('id');

            // FK -> corr_correspondencia.id_radicado (BIGINT)
            $table->bigInteger('id_correspondencia');

            // Observación
            $table->text('observacion')->nullable();

            // Estado
            $table->enum('estado', [
                'Recibido',
                'En Revisión',
                'Aprobado',
                'Negado',
                'Archivado',
                'Pendiente',
                'Completado'
            ])->default('Recibido');

            // FK -> corr_procesos.id (BIGINT UNSIGNED) ✅
            $table->unsignedBigInteger('id_proceso');

            // Notificación
            $table->boolean('notificado_email')->default(false);

            // Fecha
            $table->dateTime('fecha_gestion')->nullable();

            // Documento
            $table->string('documento_arc')->nullable();

            // Usuario
            $table->unsignedBigInteger('fk_usuario');

            $table->timestamps();

            /*
            |--------------------------------------------------------------------------
            | Foreign Keys
            |--------------------------------------------------------------------------
            */

            $table->foreign('id_correspondencia')
                  ->references('id_radicado')
                  ->on('corr_correspondencia')
                  ->onDelete('cascade');

            $table->foreign('id_proceso')
                  ->references('id')
                  ->on('corr_procesos');

            $table->foreign('fk_usuario')
                  ->references('id')
                  ->on('users');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('corr_correspondencia_proceso');
    }
};
