<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        // Tabla: WORKFLOWS
        // Descripción: Define los flujos de trabajo o proyectos dentro del tablero. 
        // Cada flujo puede tener múltiples tareas.
        Schema::create('workflows', function (Blueprint $table) {
            $table->id(); // Identificador único del flujo
            $table->string('nombre'); // Nombre del flujo/proyecto
            $table->text('descripcion')->nullable(); // Detalles del flujo
            $table->foreignId('creado_por')->constrained('users')->onDelete('cascade'); 
            // Usuario que creó el flujo (FK → users)
            $table->timestamps(); // created_at y updated_at
        });
    }

    public function down(): void {
        Schema::dropIfExists('workflows');
    }
};
