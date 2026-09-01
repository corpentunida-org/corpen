<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

// Catálogo propio de SGRH. Se puebla en el mismo up() con los 6 tipos pedidos por el usuario
// (mismo patrón "crear + sembrar en un solo paso" que
// 2026_08_31_210002_migrate_gdo_area_cargo_data_to_sgrh).
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sgrh_tipos_contrato', function (Blueprint $table) {
            $table->id();
            $table->string('nombre')->unique();
            $table->boolean('activo')->default(true);
            $table->timestamps();
        });

        DB::table('sgrh_tipos_contrato')->insert(collect([
            'Término fijo',
            'Indefinido',
            'Obra o labor',
            'Prestación de servicios',
            'Contrato de aprendizaje',
            'Contrato transitorio',
        ])->map(fn($nombre) => [
            'nombre' => $nombre,
            'activo' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ])->all());
    }

    public function down(): void
    {
        Schema::dropIfExists('sgrh_tipos_contrato');
    }
};
