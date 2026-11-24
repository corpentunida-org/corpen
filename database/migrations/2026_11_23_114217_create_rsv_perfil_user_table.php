<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('rsv_perfil_user', function (Blueprint $table) {
            $table->id();
            $table->foreignId('perfil_id')
                  ->constrained('rsv_perfiles')
                  ->cascadeOnUpdate()
                  ->restrictOnDelete();
            $table->foreignId('user_id')
                  ->constrained('users')
                  ->cascadeOnUpdate()
                  ->cascadeOnDelete();
            $table->timestamps();

            $table->unique(['perfil_id', 'user_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('rsv_perfil_user');
    }
};
