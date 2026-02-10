<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('corr_notificaciones', function (Blueprint $table) {
            $table->id();

            $table->foreignId('proceso_origen_id')
                ->constrained('corr_procesos')
                ->cascadeOnDelete();

            $table->foreignId('proceso_destino_id')
                ->constrained('corr_procesos')
                ->cascadeOnDelete();

            $table->text('mensaje');

            $table->string('estado', 20)
                ->default('pendiente'); // pendiente | enviada | leida

            $table->foreignId('usuario_destino_id')
                ->constrained('users')
                ->cascadeOnDelete();

            $table->foreignId('usuario_envia_id')
                ->constrained('users')
                ->cascadeOnDelete();

            $table->timestamp('fecha_leida')->nullable();

            $table->timestamps();

            // índices para rendimiento
            $table->index(['usuario_destino_id', 'estado']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('corr_notificaciones');
    }
};
