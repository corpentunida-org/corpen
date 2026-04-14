<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('car_comprobantes_pagos', function (Blueprint $table) {
            // id: bigint UN AI PK
            $table->id();

            // cod_ter_MaeTerceros: int
            $table->integer('cod_ter_MaeTerceros');

            // monto_pagado: int
            $table->integer('monto_pagado');

            // fecha_pago: int (Tal cual está en la imagen)
            $table->integer('fecha_pago');

            // hash_transaccion: (UNIQUE- Para evitar duplicados)
            $table->string('hash_transaccion')->unique();

            // ruta_archivo: text
            $table->text('ruta_archivo');

            // id_transaccion_bancaria: int
            $table->integer('id_transaccion_bancaria');

            // id_interaction: bing (lo tratamos como bigint)
            $table->unsignedBigInteger('id_interaction');

            // id_user: bing (lo tratamos como bigint)
            $table->unsignedBigInteger('id_user');

            // created_at & updated_at: timestamp
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('car_comprobantes_pagos');
    }
};