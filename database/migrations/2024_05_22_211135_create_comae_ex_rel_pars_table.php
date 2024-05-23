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
        Schema::create('coMaeExRelPar', function (Blueprint $table) {
            $table->bigInteger('cedula');
            $table->string('nombre');
            $table->string('parentesco');
            $table->bigInteger('cedulaAsociado');
            $table->date('fechaNacimiento');
            $table->date('fechaIngreso');
            $table->boolean('estado')->default(true);
            $table->timestamps();
            $table->foreign('cedulaAsociado')->references('cedula')->on('coMaeCli');
            $table->foreign('parentesco')->references('codPar')->on('parentescos');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('comaeExRelPar');
    }
};
