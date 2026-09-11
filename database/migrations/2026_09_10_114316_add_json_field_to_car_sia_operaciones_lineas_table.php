<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('car_sia_operaciones_lineas', function (Blueprint $table) {
            // Se agrega el campo JSON. Se recomienda hacerlo nullable
            // para no afectar los registros existentes.
            $table->json('metadata')
                  ->nullable()
                  ->after('hash_certificado');
        });
    }

    public function down(): void
    {
        Schema::table('car_sia_operaciones_lineas', function (Blueprint $table) {
            $table->dropColumn('metadata');
        });
    }
};
