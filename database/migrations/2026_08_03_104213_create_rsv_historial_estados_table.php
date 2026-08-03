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
        Schema::create('rsv_historial_estados', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_rsv_reservas')->constrained('rsv_reservas')->cascadeOnDelete();

            $table->foreignId('id_rsv_statuses_anterior')->nullable()->constrained('rsv_statuses')->restrictOnDelete();
            $table->foreignId('id_rsv_statuses_nuevo')->constrained('rsv_statuses')->restrictOnDelete();

            // Relación con users ajustada a BIGINT UNSIGNED
            $table->foreignId('id_user')->nullable()->constrained('users')->restrictOnDelete();

            $table->text('comentario')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rsv_historial_estados');
    }
};
