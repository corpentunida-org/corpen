<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        // 1. Tabla: car_sia_operaciones_alertas
        Schema::table('car_sia_operaciones_alertas', function (Blueprint $table) {
            $table->foreignId('id_user')->nullable()->after('procesado_en')->constrained('users')->nullOnDelete();
        });

        // 2. Tabla: car_sia_estados_operacion
        Schema::table('car_sia_estados_operacion', function (Blueprint $table) {
            $table->foreignId('id_user')->nullable()->after('numero_bloque')->constrained('users')->nullOnDelete();
        });

        // 3. Tabla: car_sia_tipos_operacion (Basado en el nombre definido en tu modelo)
        Schema::table('car_sia_tipos_operacion', function (Blueprint $table) {
            $table->foreignId('id_user')->nullable()->after('numero_bloque')->constrained('users')->nullOnDelete();
        });
    }

    public function down()
    {
        Schema::table('car_sia_operaciones_alertas', function (Blueprint $table) {
            $table->dropForeign(['id_user']);
            $table->dropColumn('id_user');
        });

        Schema::table('car_sia_estados_operacion', function (Blueprint $table) {
            $table->dropForeign(['id_user']);
            $table->dropColumn('id_user');
        });

        Schema::table('car_sia_tipos_operacion', function (Blueprint $table) {
            $table->dropForeign(['id_user']);
            $table->dropColumn('id_user');
        });
    }
};
