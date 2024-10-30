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
        Schema::create('EXE_CoMae_ExRelPar', function (Blueprint $table) {
            $table->integer('idrow');
            $table->bigInteger('cedula')->unique()->primary();
            $table->string('nombre');
            $table->string('cod_par', length:5);
            $table->string('tipo', length:1);
            $table->date('fec_ing');
            $table->date('fec_nac');
            $table->boolean('estado');
            $table->bigInteger('cod_cli');
            //$table->foreign('cod_cli')->references('cod_cli')->on('CoMae_ExCli');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('EXE_CoMae_ExRelPar');
    }
};
