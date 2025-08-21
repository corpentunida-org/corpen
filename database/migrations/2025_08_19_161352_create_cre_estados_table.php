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
        Schema::create('cre_estados', function (Blueprint $table) {
            // PK: id: AI
            $table->id();

            // Atributos
            $table->string('nombre');

            // FK: Clave Foránea
            // Esto establece la relación: Un estado pertenece a una etapa.
            $table->foreignId('cre_etapas_id')->constrained('cre_etapas');
            
            // Timestamps para auditoría
            $table->timestamps();

            // Opcional: Si el nombre del estado debe ser único DENTRO de cada etapa,
            // puedes añadir una restricción de unicidad compuesta.
            // $table->unique(['cre_etapas_id', 'nombre']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('cre_estados');
    }
};