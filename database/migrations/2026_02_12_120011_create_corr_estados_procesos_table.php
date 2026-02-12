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
        Schema::create('corr_estados_procesos', function (Blueprint $table) {
            $table->id(); // id (PK, Int)

            // Relaciones usando tus nombres de columna
            $table->unsignedBigInteger('id_estado'); 
            $table->unsignedBigInteger('id_proceso');
            
            $table->text('detalle')->nullable(); // detalle
            
            // Esto crea las columnas created_at y updated_at para el 'timestamp'
            $table->timestamps(); 

            // Definición de las llaves foráneas
            $table->foreign('id_estado')->references('id')->on('corr_estados')->onDelete('cascade');
            $table->foreign('id_proceso')->references('id')->on('corr_procesos')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('corr_estados_procesos');
    }
};
