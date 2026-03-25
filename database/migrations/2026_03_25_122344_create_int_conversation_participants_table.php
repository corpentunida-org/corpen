<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('int_conversation_participants', function (Blueprint $table) {
            $table->id();
            $table->foreignId('conversation_id')->constrained('int_conversations')->onDelete('cascade');
            $table->foreignId('user_id')->constrained('users'); // Asume que tu tabla de usuarios es 'users'
            $table->foreignId('role_id')->constrained('int_roles');
            $table->timestamp('joined_at')->useCurrent();
        });
    }

    public function down(): void {
        Schema::dropIfExists('int_conversation_participants');
    }
};