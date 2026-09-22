<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tercero_importaciones', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users');
            $table->string('archivo_nombre');
            $table->json('resumen');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tercero_importaciones');
    }
};
