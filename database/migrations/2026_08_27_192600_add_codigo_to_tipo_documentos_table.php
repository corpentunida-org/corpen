<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * La tabla tipo_documentos ya existía (creada en 2024_11_22_162227) pero estaba huérfana:
 * ningún modelo ni controlador la referenciaba, y sus 3 filas (id 1/2/3) no coincidían con
 * los códigos reales que usa MaeTerceros.tdoc ('13', '31', '12'...). Se verificó contra el
 * código y la base de datos real antes de tocarla — es segura de completar/resembrar.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tipo_documentos', function (Blueprint $table) {
            $table->string('codigo', 5)->unique()->nullable()->after('id');
        });

        DB::table('tipo_documentos')->truncate();

        DB::table('tipo_documentos')->insert([
            ['codigo' => '11', 'nombre' => 'Registro Civil', 'created_at' => now(), 'updated_at' => now()],
            ['codigo' => '12', 'nombre' => 'Tarjeta de Identidad', 'created_at' => now(), 'updated_at' => now()],
            ['codigo' => '13', 'nombre' => 'Cédula de Ciudadanía', 'created_at' => now(), 'updated_at' => now()],
            ['codigo' => '22', 'nombre' => 'Cédula de Extranjería', 'created_at' => now(), 'updated_at' => now()],
            ['codigo' => '31', 'nombre' => 'NIT', 'created_at' => now(), 'updated_at' => now()],
            ['codigo' => '41', 'nombre' => 'Pasaporte', 'created_at' => now(), 'updated_at' => now()],
            ['codigo' => '48', 'nombre' => 'Permiso por Protección Temporal', 'created_at' => now(), 'updated_at' => now()],
        ]);
    }

    public function down(): void
    {
        Schema::table('tipo_documentos', function (Blueprint $table) {
            $table->dropColumn('codigo');
        });
    }
};
