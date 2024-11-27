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
        Schema::create('seg_convenios', function (Blueprint $table) {
            $table->id();
            $table->integer('seg_proveedor_id');
            $table->date('fecha_inicio');
            $table->date('fecha_fin');
            $table->float('tasa_retorno');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('seg_convenios');
    }
};
