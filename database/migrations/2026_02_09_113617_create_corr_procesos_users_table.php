<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('corr_procesos_users', function (Blueprint $table) {
            $table->id();

            $table->foreignId('user_id')
                ->constrained('users')
                ->cascadeOnDelete();

            $table->foreignId('proceso_id')
                ->constrained('corr_procesos')
                ->cascadeOnDelete();

            $table->text('detalle')->nullable();

            $table->timestamps();

            // evita duplicados (mismo usuario en mismo proceso)
            $table->unique(['user_id', 'proceso_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('corr_procesos_users');
    }
};

