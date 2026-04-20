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
        Schema::create('rec_imputaciones_contables', function (Blueprint $table) {
            // ID principal (bigint UN AI PK)
            $table->id();

            // Llaves foráneas (bigint UN)
            $table->unsignedBigInteger('id_transaccion');
            $table->unsignedBigInteger('id_tercero_origen');
            $table->unsignedBigInteger('id_distrito');
            
            // ID Recibo (Se define como identificador único adicional)
            $table->unsignedBigInteger('id_recibo')->unique();

            // Campos de texto y contenido
            $table->text('concepto_contable')->comment('Ej: L.I MENORES, CTA 14 DIC, RECAUDO DANIELA');
            $table->text('link_ecm')->nullable()->comment('Link de gestión documental');
            
            // Valores financieros
            $table->integer('valor_imputado');

            // Estado de conciliación (Enum)
            $table->enum('estado_conciliacion', [
                'Pendiente', 
                'Conciliado_Auto', 
                'Conciliado_Manual', 
                'Anulado'
            ])->default('Pendiente');

            // Auditoría
            $table->timestamps();

            // Definición de llaves foráneas (ajusta los nombres de tablas si varían)
            $table->foreign('id_transaccion')
                  ->references('id_transaccion')
                  ->on('con_extractos_transacciones');
            
            // Índices adicionales para velocidad de búsqueda
            $table->index('id_tercero_origen');
            $table->index('id_distrito');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rec_imputaciones_contables');
    }
};