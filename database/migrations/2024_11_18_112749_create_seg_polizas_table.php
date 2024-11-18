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
        Schema::create('SEG_polizas', function (Blueprint $table) {
            $table->bigInteger('idPoliza')->unique()->primary();
            $table->date('fechaInicio');
            $table->date('fechaFin');
            $table->date('fechaNovedad');
            $table->string('estado');
            $table->string('descripcion');

            $table->bigInteger('idAsegurado');
            $table->foreign('idAsegurado')->references('cedula')->on('SEG_terceros');

            $table->unsignedBigInteger('idTipoPoliza');
            $table->foreign('idTipoPoliza')->references('id')->on('SEG_tipoPolizas');

            $table->unsignedBigInteger('idPlan');
            $table->foreign('idPlan')->references('id')->on('SEG_plans');

            $table->timestamps();
        });
    }
    
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('SEG_polizas');
    }
};

//2024_11_18_092414 poliza
//2024_11_18_092800 beneficiario
//2024_11_18_112749 tipo
//2024_11_18_143022 planes