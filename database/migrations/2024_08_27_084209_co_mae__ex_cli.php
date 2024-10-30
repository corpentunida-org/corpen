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
        Schema::create('EXE_CoMae_ExCli', function (Blueprint $table) {
            $table->integer('idrow');
            $table->bigInteger('cod_cli')->unique()->primary();
            $table->text('benef')->nullable();
            $table->string('cod_plan', length:5)->nullable();
            $table->date('fec_ing')->nullable();
            $table->string('cod_cco', length:10)->nullable();
            $table->boolean('estado')->default(true);
            $table->date('fec_ini')->nullable();
            $table->string('por_descto', length:5)->nullable();
            $table->string('contrato', length:5)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('EXE_CoMae_ExCli');
    }
};
