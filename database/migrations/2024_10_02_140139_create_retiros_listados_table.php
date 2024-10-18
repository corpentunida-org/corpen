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
        Schema::create('CIN_RetirosListados', function (Blueprint $table) {
            $table->id();
            $table->string('Cod_Ter');
            $table->date('fechaIngresoMinisterio')->nullable();
            $table->date('fechaRespaldo')->nullable();
            $table->boolean('Verificacion')->default(false);
            $table->date('VerificadoFecha')->nullable();
            $table->string('VerificadoUsuario')->nullable();
            $table->date('fechaPrimerAporte')->nullable();
            $table->string('ObservacionActualizacion')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('CIN_RetirosListados');
    }
};
