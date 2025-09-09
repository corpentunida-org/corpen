<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        // Tabla: TASK_COMMENTS
        // Descripción: Almacena los comentarios o notas dejadas en cada tarea.
        Schema::create('task_comments', function (Blueprint $table) {
            $table->id(); // Identificador único del comentario
            $table->foreignId('task_id')->constrained('tasks')->onDelete('cascade'); 
            // Tarea relacionada (FK → tasks)
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade'); 
            // Usuario que hizo el comentario (FK → users)
            $table->text('comentario'); // Texto del comentario
            $table->timestamps(); // created_at y updated_at
        });
    }

    public function down(): void {
        Schema::dropIfExists('task_comments');
    }
};
