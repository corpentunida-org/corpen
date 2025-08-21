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
        Schema::create('cre_pagares', function (Blueprint $table) {
            // Columnas del diagrama
            $table->id(); // Llave primaria auto-incremental (PK)
            $table->string('id_unico_documento')->unique()->comment('Identificador único del pagaré, ej: UUID o Folio');

            // --- Columnas adicionales sugeridas ---

            // Relación directa con el crédito
            $table->foreignId('cre_credito_id')->constrained('cre_creditos')->onDelete('cascade');

            // Condiciones financieras del pagaré
            $table->decimal('valor_capital', 15, 2)->comment('Monto principal del crédito');
            $table->decimal('tasa_interes_nominal', 5, 2)->comment('Tasa de interés pactada (ej: 21.50%)');
            $table->decimal('tasa_interes_mora', 5, 2)->comment('Tasa de interés por pagos atrasados');
            $table->integer('numero_cuotas');

            // Fechas clave
            $table->date('fecha_emision')->comment('Fecha en que se firmó el pagaré');
            $table->date('fecha_vencimiento')->comment('Fecha del último pago');

            // Información adicional
            $table->string('lugar_firma');
            $table->string('estado')->default('vigente')->comment('Ej: vigente, pagado, en mora, cancelado');
            
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
        Schema::dropIfExists('cre_pagares');
    }
};