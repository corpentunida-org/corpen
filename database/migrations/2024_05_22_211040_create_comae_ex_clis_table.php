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
        Schema::create('coMae_ExCli', function (Blueprint $table) {
            $table->bigInteger('cedula')->primary();
            $table->string('apellido');
            $table->string('nombre');
            $table->integer('distrito_id');
            $table->string('direccion')->nullable();
            $table->integer('ciudad_id')->nullable();
            $table->boolean('estado')->default(true);
            $table->string('celular')->nullable();
            $table->string('email')->nullable();
            $table->date('fechaNacimiento');
            $table->text('observacion_familia')->nullable();
            $table->text('observacion')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('coMaeCli');
    }
};
