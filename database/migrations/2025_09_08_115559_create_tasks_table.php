<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        // Tabla: TASKS
        // Descripción: Contiene las tareas o ítems dentro de un flujo de trabajo.
        Schema::create('tasks', function (Blueprint $table) {
            $table->id(); // Identificador único de la tarea
            $table->string('titulo'); // Nombre de la tarea
            $table->text('descripcion')->nullable(); // Detalle de la tarea
            $table->enum('estado', ['pendiente','en_proceso','revisado','completado'])->default('pendiente');
            // Estado de la tarea
            $table->enum('prioridad', ['baja','media','alta'])->default('media'); 
            // Prioridad de la tarea
            $table->dateTime('fecha_limite')->nullable(); // Fecha límite de entrega
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade'); 
            // Responsable de la tarea (FK → users)
            $table->foreignId('workflow_id')->nullable()->constrained('workflows')->onDelete('cascade'); 
            // Flujo al que pertenece (FK → workflows)
            $table->timestamps(); // created_at y updated_at
        });
    }

    public function down(): void {
        Schema::dropIfExists('tasks');
    }
};
