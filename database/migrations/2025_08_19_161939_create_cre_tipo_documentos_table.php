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
        Schema::create('cre_tipo_documentos', function (Blueprint $table) {
            // PK: id: AI
            $table->id();

            // Atributo: nombre: STRING
            $table->string('nombre')->unique(); // El nombre del tipo de documento debe ser único

            // FK: Clave Foránea que indica a qué etapa pertenece este tipo de documento
            $table->foreignId('cre_etapas_id')->constrained('cre_etapas');
            
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
        Schema::dropIfExists('cre_tipo_documentos');
    }
};