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
        Schema::create('cre_etapas', function (Blueprint $table) {
            // PK: id: AI
            $table->id();

            // Atributos
            $table->string('nombre')->unique(); // El nombre de la etapa debe ser único
            $table->string('descripcion')->nullable(); // La descripción puede ser opcional

            // Timestamps para auditoría
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
        Schema::dropIfExists('cre_etapas');
    }
};