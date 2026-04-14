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
        Schema::create('con_extractos_transacciones', function (Blueprint $table) {
            // id_transaccion: bigint UN AI PK
            $table->bigIncrements('id_transaccion');

            // id_con_cuentas_bancaria: bigint UN
            $table->unsignedBigInteger('id_con_cuentas_bancaria');
            
            // hash_transaccion: (UNIQUE) (fecha_movimiento+valor_ingreso+descripcion_banco)
            $table->string('hash_transaccion')->unique();

            // fecha_movimiento: datetime
            $table->dateTime('fecha_movimiento');

            // valor_ingreso: int
            $table->integer('valor_ingreso');

            // descripcion_banco: text
            $table->text('descripcion_banco');

            // estado_conciliacion: Enum
            $table->enum('estado_conciliacion', [
                'Pendiente', 
                'Conciliado_Auto', 
                'Conciliado_Manual', 
                'Anulado'
            ])->default('Pendiente');

            // created_at & updated_at
            $table->timestamps();

            // Relación (Opcional si existe la tabla anterior)
            $table->foreign('id_con_cuentas_bancaria', 'fk_extractos_cuentas')
                  ->references('id')
                  ->on('con_cuentas_bancarias')
                  ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('con_extractos_transacciones');
    }
};