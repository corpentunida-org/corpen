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
        Schema::create('geo_paises', function (Blueprint $table) {
            // codigo_iso como Primary Key (Ej: 'CO', 'MEX')
            $table->string('codigo_iso', 3)->primary(); 
            $table->string('nombre', 100);
            
            $table->timestamps(); // Opcional: created_at y updated_at
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('geo_paises');
    }
};
