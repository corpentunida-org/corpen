<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('rsv_usuario_inmueble', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')
                  ->constrained('users')
                  ->cascadeOnUpdate()
                  ->cascadeOnDelete();
            $table->foreignId('inmueble_id')
                  ->constrained('rsv_catalogo_inmueble')
                  ->cascadeOnUpdate()
                  ->restrictOnDelete();
            $table->boolean('bloqueado')->default(true);
            $table->timestamps();

            $table->unique(['user_id', 'inmueble_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('rsv_usuario_inmueble');
    }
};
