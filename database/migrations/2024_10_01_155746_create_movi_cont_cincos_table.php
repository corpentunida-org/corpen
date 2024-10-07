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
        Schema::create('MoviCont', function (Blueprint $table) {
            $table->id();
            $table->string('CodComprob')->nullable();
            $table->string('NumComprob')->nullable();
            $table->string('ItemComprob')->nullable();
            $table->date('Fecha')->nullable();
            $table->string('Cuenta')->nullable();
            $table->string('DocRef')->nullable();            
            $table->string('Base')->nullable();
            $table->string('Cedula')->nullable();
            $table->string('CentroCosto')->nullable();
            $table->string('VrDebitos')->nullable();
            $table->string('VrCreditos')->nullable();
            $table->string('UsuariosAdd')->nullable();
            $table->string('DocSoporte')->nullable();
            $table->string('Observacion')->nullable();
            $table->string('AñoIncio')->nullable();
            $table->string('MesIncio')->nullable();
            $table->string('AñoFin')->nullable();
            $table->string('MesFin')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('MoviCont');
    }
};
