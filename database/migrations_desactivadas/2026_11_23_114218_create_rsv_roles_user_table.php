<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('rsv_roles_user', function (Blueprint $table) {
            $table->id();
            $table->foreignId('role_id')
                  ->constrained('rsv_roles')
                  ->cascadeOnUpdate()
                  ->restrictOnDelete();
            $table->foreignId('user_id')
                  ->constrained('users')
                  ->cascadeOnUpdate()
                  ->cascadeOnDelete();
            $table->timestamps();

            $table->unique(['role_id', 'user_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('rsv_roles_user');
    }
};
