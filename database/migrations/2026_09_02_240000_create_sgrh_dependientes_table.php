<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// Dependientes económicos del colaborador (hijos, cónyuge, padres, etc.). cascadeOnDelete a
// propósito (a diferencia de sgrh_contratos, que usa RESTRICT): un dependiente no tiene
// sentido sin el colaborador al que pertenece, no es dato de auditoría independiente.
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sgrh_dependientes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('empleado_id')->constrained('sgrh_empleados')->cascadeOnDelete();
            $table->string('nombre1');
            $table->string('nombre2')->nullable();
            $table->string('apellido1');
            $table->string('apellido2')->nullable();
            $table->date('fecha_nacimiento');
            // Mismo criterio que MaeTerceros.sexo (V/H), no una columna nueva de convención
            // distinta.
            $table->string('genero', 1)->nullable();
            // Código del catálogo App\Models\Maestras\Parentesco (mismo que usa MaeTerceros
            // para el parentesco del cónyuge) — no se crea un catálogo nuevo para esto.
            $table->string('parentesco', 10)->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sgrh_dependientes');
    }
};
