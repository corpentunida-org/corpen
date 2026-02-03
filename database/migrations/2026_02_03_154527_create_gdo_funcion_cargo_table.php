<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('gdo_funcion_cargo', function (Blueprint $table) {
            $table->id();
            
            // Llaves foráneas
            $table->foreignId('gdo_funcion_id')
                  ->constrained('gdo_funcion')
                  ->onDelete('cascade');
                  
            $table->foreignId('gdo_cargo_id')
                  ->constrained('gdo_cargo') // Asegúrate que esta tabla exista
                  ->onDelete('cascade');

            $table->boolean('estado')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('gdo_funcion_cargo');
    }
};