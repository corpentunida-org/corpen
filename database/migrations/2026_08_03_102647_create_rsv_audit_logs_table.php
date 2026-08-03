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
        Schema::create('rsv_audit_logs', function (Blueprint $table) {
            $table->id();
            // Vinculación sugerida con users usando BIGINT UNSIGNED en lugar de INT
            $table->foreignId('id_user')->nullable()->constrained('users')->nullOnDelete();

            $table->string('tabla_afectada', 100);
            $table->unsignedBigInteger('registro_id');
            $table->string('accion', 50);
            $table->json('datos_anteriores')->nullable();
            $table->json('datos_nuevos')->nullable();
            $table->string('ip_address', 45);
            $table->timestamps();

            // Índice para búsquedas rápidas de auditoría
            $table->index(['tabla_afectada', 'registro_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rsv_audit_logs');
    }
};
