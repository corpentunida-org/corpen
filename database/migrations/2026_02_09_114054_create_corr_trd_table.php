<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('corr_trd', function (Blueprint $table) {
            $table->id('id_trd');

            $table->string('serie_documental', 100);

            $table->integer('tiempo_gestion')
                ->comment('Años en oficina');

            $table->integer('tiempo_central')
                ->comment('Años en bodega');

            $table->enum('disposicion_final', ['conservar', 'eliminar']);

            $table->foreignId('usuario_id')
                ->constrained('users')
                ->cascadeOnDelete();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('corr_trd');
    }
};
