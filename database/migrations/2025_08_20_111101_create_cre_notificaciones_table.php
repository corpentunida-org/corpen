<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('cre_notificaciones', function (Blueprint $table) {
            // PK: id: AI
            $table->id();

            // --- FK: Claves Foráneas ---

            // Conexión con el crédito (esto está bien)
            $table->foreignId('cre_creditos_id')
                  ->constrained('cre_creditos')
                  ->onDelete('cascade');

            // --- INICIO DE LA CORRECCIÓN ---
            // Conexión con el cliente/tercero vía 'cod_ter'
            // 1. La columna local debe ser compatible (BIGINT)
            $table->bigInteger('mae_terceros_cod_ter');

            // 2. La referencia debe apuntar a la columna correcta: 'cod_ter'
            $table->foreign('mae_terceros_cod_ter')
                  ->references('cod_ter')->on('MaeTerceros');
            // --- FIN DE LA CORRECCIÓN ---

            // --- Atributos de la Notificación ---
            $table->string('asunto');
            $table->text('mensaje');
            $table->string('canal');
            $table->enum('estado', ['pendiente', 'enviada', 'entregada', 'fallida', 'leida'])
                  ->default('pendiente');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('cre_notificaciones');
    }
};