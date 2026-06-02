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
        Schema::create('geo_ciudades', function (Blueprint $table) {
            $table->id('id_ciudad'); // BIGINT PK
            $table->string('nombre', 100);
            
            // Llave foránea
            $table->unsignedBigInteger('id_subregion');
            $table->foreign('id_subregion')->references('id_subregion')->on('geo_subregiones')->onDelete('cascade');
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('geo_ciudades');
    }
};
