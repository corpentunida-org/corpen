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
        Schema::create('rsv_itinerarios_eventos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_rsv_reservas')->constrained('rsv_reservas')->cascadeOnDelete();

            $table->string('titulo_evento', 255);
            $table->dateTime('hora_inicio');
            $table->dateTime('hora_fin');
            $table->json('distribucion_detalle')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rsv_itinerarios_eventos');
    }
};
