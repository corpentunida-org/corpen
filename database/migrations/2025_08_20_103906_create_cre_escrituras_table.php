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
        Schema::create('cre_escrituras', function (Blueprint $table) {
            // Columnas del diagrama
            $table->id(); // Llave primaria auto-incremental (PK)
            $table->string('id_unico_documento')->unique()->comment('Identificador único del documento, ej: UUID o Folio');

            // --- Columnas adicionales sugeridas ---

            // Relación con el crédito que respalda
            $table->foreignId('cre_credito_id')->constrained('cre_creditos');

            // Datos de la notaría y registro
            $table->integer('numero_notaria');
            $table->string('ciudad_notaria');
            $table->string('folio_matricula_inmobiliaria')->nullable()->comment('Número de identificación del inmueble');
            $table->string('oficina_registro_instrumentos');

            // Fechas importantes
            $table->date('fecha_constitucion')->comment('Fecha de creación de la escritura');
            $table->date('fecha_registro')->nullable()->comment('Fecha de registro oficial');
            
            // Valores
            $table->decimal('valor_gravamen', 15, 2)->comment('Valor del embargo o hipoteca');

            // Estado de la escritura
            $table->string('estado')->default('activa')->comment('Ej: activa, cancelada, en proceso');
            
            // Timestamps (created_at y updated_at) para auditoría
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
        Schema::dropIfExists('cre_escrituras');
    }
};