<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// Ajustes puntuales sobre los festivos colombianos que FestivoColombiaCalculador calcula por
// algoritmo — 'agregado' suma una fecha extra (festivo local/empresarial), 'excluido' anula una
// fecha que el cálculo automático marcó como festiva pero no debe aplicar. No se cargan los
// festivos "normales" aquí: esta tabla es solo para las excepciones que RRHH decida.
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sgrh_festivos_ajustes', function (Blueprint $table) {
            $table->id();
            $table->date('fecha');
            $table->enum('tipo', ['agregado', 'excluido']);
            $table->string('descripcion')->nullable();
            $table->foreignId('user_id')->constrained('users');
            $table->timestamps();

            $table->unique(['fecha', 'tipo']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sgrh_festivos_ajustes');
    }
};
