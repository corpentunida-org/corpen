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
        Schema::create('cre_observaciones', function (Blueprint $table) {
            // PK: id: AI (Llave Primaria)
            $table->id();

            // FK: Claves Foráneas
            // Conecta con el crédito al que pertenece la observación.
            $table->foreignId('cre_creditos_id')
                  ->constrained('cre_creditos')
                  ->onDelete('cascade'); // Si se borra un crédito, se borran sus observaciones.

            // Conecta con el usuario que registra la observación.
            // Es 'nullable' para permitir observaciones automáticas del sistema.
            $table->foreignId('user_id')->nullable()->constrained('users');

            // Atributos de la observación
            $table->string('asunto')->comment('Título o resumen corto de la observación.');
            $table->string('categoria')->default('general')->comment('Ej: Llamada, Visita, Correo, Alerta Sistema, Nota Legal');
            $table->text('observacion'); // Cuerpo principal de la observación.

            // Timestamps (created_at y updated_at) para saber cuándo se hizo cada observación.
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
        Schema::dropIfExists('cre_observaciones');
    }
};