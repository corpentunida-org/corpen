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
        Schema::create('distritos', function (Blueprint $table) {
            $table->id();
            $table->integer('cod_dist');
            $table->string('nom_dist');
            $table->string('compuest');
            $table->string('cod_supervisor')->nullable();
            $table->string('cod_primerpresb')->nullable();
            $table->string('cod_segundopresb')->nullable();
            $table->string('cod_tercerpresb')->nullable();
            $table->string('cod_secrepresb')->nullable();
            $table->string('cod_tesopresb')->nullable();
            $table->string('cod_fiscal')->nullable();
            $table->string('cod_asesorcorpen')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('distritos');
    }
};
