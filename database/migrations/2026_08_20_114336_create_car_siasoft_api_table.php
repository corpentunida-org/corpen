<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('car_sia_api', function (Blueprint $table) {
            $table->id(); // Identificador interno autoincremental

            // Campos de la tabla convertidos a string (varchar) para ingesta segura
            $table->string('is_selected')->nullable();
            $table->text('detalle')->nullable(); // text por si el string supera los 255 caracteres
            $table->text('log_rq')->nullable(); // text por si el log es muy largo
            $table->string('anular')->nullable();
            $table->string('id_factura')->nullable();
            $table->string('cuenta')->nullable();
            $table->string('nombre_cuenta')->nullable();
            $table->string('tercero_base')->nullable();
            $table->string('tercero')->nullable();
            $table->string('nombre_tercero')->nullable();
            $table->string('tercero_cco')->nullable();
            $table->string('doc_mov')->nullable();
            $table->string('cco')->nullable();
            $table->string('trn')->nullable();
            $table->string('numero_documento')->nullable(); // Reemplaza "# Documento"
            $table->string('pagare')->nullable();
            $table->string('cuota')->nullable();
            $table->string('anio')->nullable(); // Reemplaza "Año" para evitar la 'ñ'
            $table->string('mes')->nullable();
            $table->string('fecha_venci')->nullable();
            $table->string('estado')->nullable();
            $table->string('contabilizado')->nullable();
            $table->text('nota')->nullable(); // text por si las notas son extensas
            $table->string('fecha_trn_banco')->nullable();
            $table->string('valor_inicial')->nullable();
            $table->string('valor_pago_ofic')->nullable();
            $table->string('valor')->nullable();
            $table->string('valor_banco')->nullable();
            $table->string('uid_banco')->nullable();
            $table->string('banco')->nullable();
            $table->string('fecha_ad')->nullable();
            $table->string('fecha_edit')->nullable();
            $table->string('tipo')->nullable();
            $table->string('id_cab')->nullable();
            // Nota: En tu imagen, "Cuota" aparece dos veces. Lo omito aquí porque Laravel no permite columnas duplicadas.
            $table->string('id_reg_cab_ref')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('car_sia_api');
    }
};
