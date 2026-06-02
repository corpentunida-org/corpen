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
        Schema::create('geo_direcciones', function (Blueprint $table) {
            $table->id('id_direccion'); // BIGINT PK
            $table->string('calle', 150);
            $table->string('numero', 50)->nullable();
            $table->string('codigo_postal', 20)->nullable();
            
            // Llave foránea
            $table->unsignedBigInteger('id_ciudad');
            $table->foreign('id_ciudad')->references('id_ciudad')->on('geo_ciudades')->onDelete('cascade');
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('geo_direcciones');
    }
};
