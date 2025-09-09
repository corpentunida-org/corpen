<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        // Tabla: TASK_HISTORIES
        // Descripción: Registra el historial de cambios de cada tarea para auditoría.
        Schema::create('task_histories', function (Blueprint $table) {
            $table->id(); // Identificador único del registro
            $table->foreignId('task_id')->constrained('tasks')->onDelete('cascade'); 
            // Tarea relacionada (FK → tasks)
            $table->string('estado_anterior'); // Estado previo
            $table->string('estado_nuevo'); // Estado nuevo
            $table->foreignId('cambiado_por')->constrained('users')->onDelete('cascade'); 
            // Usuario que realizó el cambio (FK → users)
            $table->timestamp('fecha_cambio')->useCurrent(); // Fecha y hora del cambio
        });
    }

    public function down(): void {
        Schema::dropIfExists('task_histories');
    }
};
