<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('inv_subgrupos', function (Blueprint $table) {
            $table->id();
            $table->string('nombre'); // Ej: Portátiles, Sillas Ergonómicas
            $table->text('descripcion')->nullable(); 

            // Las 3 Relaciones Padre según diagrama
            // Verificacion de que las tablas inv_tipos, inv_lineas e inv_grupos existen.
            $table->foreignId('id_InvTipos')->constrained('inv_tipos');
            $table->foreignId('id_InvLineas')->constrained('inv_lineas');
            $table->foreignId('id_InvGrupos')->constrained('inv_grupos');
            
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('inv_subgrupos');
    }
};
