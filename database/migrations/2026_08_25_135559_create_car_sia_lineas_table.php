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
        Schema::create('car_sia_lineas', function (Blueprint $table) {
            // Si usas UUID como clave primaria en PostgreSQL, cambia esto por $table->uuid('id')->primary();
            $table->id(); 
            
            // Usamos 'string' para la cuenta por si tiene ceros a la izquierda o caracteres especiales
            $table->string('cuenta'); 
            
            $table->string('nombre');
            
            // Crea automáticamente los campos 'created_at' y 'updated_at'
            $table->timestamps(); 
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('car_sia_lineas');
    }
};