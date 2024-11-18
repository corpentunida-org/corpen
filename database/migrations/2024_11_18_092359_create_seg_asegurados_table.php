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
        Schema::create('SEG_asegurados', function (Blueprint $table) {
            $table->bigInteger('cedula');
            $table->foreign('cedula')->references('cedula')->on('SEG_terceros')->onDelete('cascade');
            $table->string('tipo');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('SEG_asegurados');
    }
};
