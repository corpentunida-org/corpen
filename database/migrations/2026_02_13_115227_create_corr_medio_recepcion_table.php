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
        Schema::create('corr_medio_recepcion', function (Blueprint $table) {
            $table->id(); // PK, Int (Auto-incremental)
            
            $table->string('codigo', 50)->unique();
            $table->string('nombre', 255);
            $table->text('descripcion')->nullable();
            
            // Campo 'activo': 1 para activo, 0 para inactivo
            $table->boolean('activo')->default(1); 
            
            // Crea 'created_at' y 'updated_at' (equivale al timestamp de la imagen)
            $table->timestamps(); 
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('corr_medio_recepcion');
    }
};