<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('car_sia_operaciones', function (Blueprint $table) {
            $table->id(); // BIGINT UNSIGNED, PK

            $table->string('numero_radicado', 50)->unique(); // Radicado único
            $table->string('numero_bloque', 50)->index(); // Identificador de bloque indexado

            // Relación con la tabla de facturas (car_siasoft_api / car_sia_api)
            $table->unsignedBigInteger('id_factura'); // FK, NOT NULL

            // Relación específica con la tabla MaeTerceros apuntando a cod_ter
            $table->bigInteger('id_tercero');  // FK, NOT NULL
            $table->foreign('id_tercero')
                  ->references('cod_ter')
                  ->on('MaeTerceros'); //

            $table->timestamps(); // created_at, updated_at
            $table->softDeletes(); // deleted_at
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('car_sia_operaciones');
    }
};
