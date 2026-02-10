<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('corr_comunicaciones_salida', function (Blueprint $table) {

            // PK
            $table->id('id_respuesta');

            // FK -> corr_correspondencia.id_radicado (BIGINT)
            $table->bigInteger('id_correspondencia');

            // Número de oficio
            $table->string('nro_oficio_salida', 50)->unique();

            // Cuerpo del documento
            $table->longText('cuerpo_carta');

            // PDF generado
            $table->string('ruta_pdf')->nullable();

            // Fecha de generación
            $table->dateTime('fecha_generacion')->nullable();

            // Estado
            $table->enum('estado_envio', [
                'Generado',
                'Enviado por Email',
                'Notificado Físicamente'
            ])->default('Generado');

            // Plantilla
            $table->unsignedBigInteger('id_plantilla')->nullable();

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

            $table->foreign('fk_usuario')
                  ->references('id')
                  ->on('users');

            $table->foreign('id_plantilla')
                  ->references('id')
                  ->on('corr_plantillas')
                  ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('corr_comunicaciones_salida');
    }
};
