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
        Schema::create('car_sia_operaciones_logs', function (Blueprint $table) {
            $table->id();
            $table->string('numero_bloque', 50)->index();
            $table->unsignedBigInteger('id_car_sia_operaciones_lineas')->nullable();
            $table->unsignedBigInteger('id_car_sia_origenes_evento');
            $table->unsignedBigInteger('id_car_sia_eventos_auditoria');
            $table->unsignedBigInteger('id_user')->nullable();
            $table->string('ip', 45)->nullable();
            $table->jsonb('detalles_ejecucion')->nullable();

            $table->timestamps();
            $table->softDeletes();

            // Foreign Key Constraints
            // Explicit names are provided to avoid PostgreSQL's 63-byte identifier limit
            $table->foreign('id_car_sia_operaciones_lineas', 'fk_log_operaciones_lineas')
                  ->references('id')->on('car_sia_operaciones_lineas')
                  ->nullOnDelete();

            $table->foreign('id_car_sia_origenes_evento', 'fk_log_origenes_evento')
                  ->references('id')->on('car_sia_origenes_evento')
                  ->cascadeOnDelete(); // Or restrictOnDelete() depending on your business logic

            $table->foreign('id_car_sia_eventos_auditoria', 'fk_log_eventos_auditoria')
                  ->references('id')->on('car_sia_eventos_auditoria')
                  ->cascadeOnDelete();

            $table->foreign('id_user', 'fk_log_user')
                  ->references('id')->on('users')
                  ->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('car_sia_operaciones_logs');
    }
};
