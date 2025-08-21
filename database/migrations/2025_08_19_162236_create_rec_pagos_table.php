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
        Schema::create('rec_pagos', function (Blueprint $table) {
            // PK: Id: AI
            $table->id();

            // Atributos
            $table->integer('cuota');
            
            // Usamos DECIMAL para valores monetarios por su precisión
            $table->decimal('valor_pagado', 15, 2); 

            // rc: CADENA - Probablemente "Recibo de Caja", debe ser único
            $table->string('rc')->unique(); 

            // comprobante: STRING - Puede ser la ruta a un archivo, lo hacemos opcional
            $table->string('comprobante')->nullable();

            // FK: Clave Foránea que conecta con el crédito al que pertenece el pago
            $table->foreignId('cre_creditos_id')
                  ->constrained('cre_creditos')
                  ->onDelete('restrict'); // Evita borrar un crédito si tiene pagos registrados

            // Timestamps para saber la fecha exacta del recaudo
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
        Schema::dropIfExists('rec_pagos');
    }
};