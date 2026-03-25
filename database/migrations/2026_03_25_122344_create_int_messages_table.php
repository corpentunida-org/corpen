<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('int_messages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('conversation_id')->constrained('int_conversations');
            $table->foreignId('user_id')->constrained('users');
            $table->text('body');
            $table->string('attachment')->nullable();
            
            // Relación para hilos de respuesta
            $table->unsignedBigInteger('parent_id')->nullable();
            $table->foreign('parent_id')->references('id')->on('int_messages');
            
            $table->timestamps();
        });
    }

    public function down(): void {
        Schema::dropIfExists('int_messages');
    }
};