<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('wor_usuarios', function (Blueprint $table) {
            $table->id();

            // 1. Relación con el Usuario
            // Asume que la tabla de usuarios es 'users'. Si es otra, cámbialo dentro de constrained('...')
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');

            // 2. Relación con el Workflow (Proyecto)
            // IMPORTANTE: Como tu modelo Workflow usa la tabla 'wor_workflows', debemos especificarla aquí.
            // Si solo pusiéramos constrained(), Laravel buscaría la tabla 'workflows' y daría error.
            $table->foreignId('workflow_id')->constrained('wor_workflows')->onDelete('cascade');

            $table->timestamps();

            // 3. Evitar duplicados: Un usuario no puede estar dos veces en el mismo flujo
            $table->unique(['user_id', 'workflow_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('wor_usuarios');
    }
};