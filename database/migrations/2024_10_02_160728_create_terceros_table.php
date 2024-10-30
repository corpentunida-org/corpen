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
        Schema::create('CIN_Terceros', function (Blueprint $table) {
            $table->id();
            $table->string('Cod_Ter')->nullable();
            $table->string('Nom_Ter')->nullable();
            $table->string('Email')->nullable();
            $table->date('Fec_Ing')->nullable();
            $table->string('Dir')->nullable();
            $table->string('Cel')->nullable();
            $table->date('fec_Nac')->nullable();
            $table->date('Fec_Minis')->nullable();
            $table->date('Fecha_Ipuc')->nullable();
            $table->date('Fec_Aport')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('CIN_Terceros');
    }
};
