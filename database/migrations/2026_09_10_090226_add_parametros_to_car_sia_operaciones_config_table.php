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
        Schema::table('car_sia_operaciones_config', function (Blueprint $table) {
            // Agregamos la columna JSON. Puede ser nullable por si tienes registros antiguos.
            $table->json('parametros')->nullable()->after('id_car_sia_config');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('car_sia_operaciones_config', function (Blueprint $table) {
            $table->dropColumn('parametros');
        });
    }
};
