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
        Schema::create('rsv_reviews', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_rsv_reservas')->constrained('rsv_reservas')->cascadeOnDelete();
            $table->foreignId('id_user_autor')->constrained('users')->cascadeOnDelete();
            $table->foreignId('id_rsv_tipo_receptor')->constrained('rsv_tipo_receptor')->restrictOnDelete();

            $table->string('tipo_evaluacion', 50);
            $table->integer('puntuacion');
            $table->text('comentario')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rsv_reviews');
    }
};
