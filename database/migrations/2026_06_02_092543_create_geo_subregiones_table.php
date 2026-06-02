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
        Schema::create('geo_subregiones', function (Blueprint $table) {
            $table->id('id_subregion'); // BIGINT PK
            $table->string('nombre', 100);
            $table->string('codigo', 50)->nullable();
            
            // Llave foránea
            $table->unsignedBigInteger('id_region');
            $table->foreign('id_region')->references('id_region')->on('geo_regiones')->onDelete('cascade');
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('geo_subregiones');
    }
};
