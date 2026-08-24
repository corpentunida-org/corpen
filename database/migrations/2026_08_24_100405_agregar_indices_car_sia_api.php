<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('car_sia_api', function (Blueprint $table) {
            // Creamos los índices para que las búsquedas vuelen
            $table->index(['estado', 'anular'], 'idx_estado_anular');
            $table->index('tercero', 'idx_tercero');
            $table->index('id_factura', 'idx_factura');
        });
    }

    public function down()
    {
        Schema::table('car_sia_api', function (Blueprint $table) {
            $table->dropIndex('idx_estado_anular');
            $table->dropIndex('idx_tercero');
            $table->dropIndex('idx_factura');
        });
    }
};
