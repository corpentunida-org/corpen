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
        Schema::create('geo_regiones', function (Blueprint $table) {
            // BIGINT Primary Key
            $table->id('id_region'); 
            $table->string('nombre', 100);
            $table->string('codigo_iso', 10)->nullable();
            
            // Llave foránea (Foreign Key)
            $table->string('iso_pais', 3);
            $table->foreign('iso_pais')->references('codigo_iso')->on('geo_paises')->onDelete('cascade');
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('geo_regiones');
    }
};
