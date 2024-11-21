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
        Schema::create('SEG_terceros', function (Blueprint $table) {
            /* $table->id(); */
            $table->bigInteger('cedula')->unique()->primary();
            $table->string('nombre');
            $table->string('fechaNacimiento')->nullable();
            $table->string('telefono')->nullable();
            $table->string('genero');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('SEG_terceros');
    }
};
