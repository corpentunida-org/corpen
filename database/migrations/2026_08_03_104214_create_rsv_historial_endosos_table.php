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
        Schema::create('rsv_historial_endosos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_rsv_reservas')->constrained('rsv_reservas')->cascadeOnDelete();

            $table->foreignId('id_user_anterior')->constrained('users')->restrictOnDelete();
            $table->foreignId('id_user_nuevo')->constrained('users')->restrictOnDelete();

            // Corrección ortográfica aplicada (autorizado vs autoizado)
            $table->foreignId('id_user_autorizado_por')->constrained('users')->restrictOnDelete();

            $table->text('motivo_endoso');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rsv_historial_endosos');
    }
};
