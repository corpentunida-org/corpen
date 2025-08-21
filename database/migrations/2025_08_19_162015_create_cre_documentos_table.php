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
        Schema::create('cre_documentos', function (Blueprint $table) {
            // PK: id: AI
            $table->id();

            // Atributos
            $table->string('ruta_archivo');
            $table->date('fecha_subida');
            $table->text('observaciones')->nullable(); // TEXT para notas largas y opcionales

            // FK: Claves Foráneas
            $table->foreignId('cre_creditos_id')->constrained('cre_creditos')->onDelete('cascade');
            $table->foreignId('cre_tipo_documentos_id')->constrained('cre_tipo_documentos');
            
            // FK: id_unico_documento - Interpretado como un identificador único para el archivo
            $table->uuid('id_unico_documento')->unique()->comment('Identificador universal único para el archivo');

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
        Schema::dropIfExists('cre_documentos');
    }
};