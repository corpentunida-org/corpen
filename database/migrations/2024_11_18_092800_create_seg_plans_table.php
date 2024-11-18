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
        Schema::create('SEG_plans', function (Blueprint $table) {
            $table->id();
            $table->string('nombrePlan');
            $table->string('valorPlan');

            $table->unsignedBigInteger('idTipoPoliza');
            $table->foreign('idTipoPoliza')->references('id')->on('SEG_tipoPolizas');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('SEG_plans');
    }
};
