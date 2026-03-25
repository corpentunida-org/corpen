<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('int_conversations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('workspace_id')->constrained('int_workspaces');
            $table->string('type'); // 'direct', 'group', 'contextual'
            $table->string('visibility')->default('internal'); // 'internal', 'external'
            $table->string('name')->nullable();
            
            // Campos para el polimorfismo
            $table->string('chatable_type')->nullable();
            $table->unsignedBigInteger('chatable_id')->nullable();
            
            $table->timestamps();
            
            // Índice para mejorar velocidad en búsquedas contextuales
            $table->index(['chatable_type', 'chatable_id']);
        });
    }

    public function down(): void {
        Schema::dropIfExists('int_conversations');
    }
};
