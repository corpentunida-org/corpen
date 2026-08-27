<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * No existía ningún catálogo de estado civil. El único precedente en el proyecto era un
 * @switch hardcodeado en maestras/terceros/show.blade.php con códigos '1'-'5' para
 * MaeTerceros.est_civil — se reutiliza ese mismo mapeo aquí en vez de inventar uno nuevo.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('estados_civiles', function (Blueprint $table) {
            $table->id();
            $table->string('codigo', 5)->unique();
            $table->string('nombre');
            $table->timestamps();
        });

        DB::table('estados_civiles')->insert([
            ['codigo' => '1', 'nombre' => 'Soltero(a)', 'created_at' => now(), 'updated_at' => now()],
            ['codigo' => '2', 'nombre' => 'Casado(a)', 'created_at' => now(), 'updated_at' => now()],
            ['codigo' => '3', 'nombre' => 'Viudo(a)', 'created_at' => now(), 'updated_at' => now()],
            ['codigo' => '4', 'nombre' => 'Unión Libre', 'created_at' => now(), 'updated_at' => now()],
            ['codigo' => '5', 'nombre' => 'Divorciado(a)', 'created_at' => now(), 'updated_at' => now()],
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('estados_civiles');
    }
};
