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
        Schema::create('rsv_tarifas_temporadas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_rsv_catalogo_inmueble')->constrained('rsv_catalogo_inmueble')->cascadeOnDelete();
            $table->string('nombre_temporada', 150);
            $table->date('fecha_inicio');
            $table->date('fecha_fin');
            $table->decimal('precio_noche', 10, 2);
            $table->decimal('precio_fin_semana', 10, 2);
            $table->boolean('active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rsv_tarifas_temporadas');
    }
};
