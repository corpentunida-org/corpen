<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('corr_procesos', function (Blueprint $table) {
            $table->id();

            $table->foreignId('flujo_id')
                ->constrained('corr_flujo_de_trabajo')
                ->cascadeOnDelete();

            $table->text('detalle')->nullable();

            $table->foreignId('usuario_creador_id')
                ->constrained('users')
                ->cascadeOnDelete();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('corr_procesos');
    }
};
