<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// Alcance de un decreto de vacaciones colectivas cuando no es 'empresa' (ver
// sgrh_vacacion_colectivas.alcance) — tabla plana, no polimórfica de Eloquent: solo hay 2
// tipos de referencia posibles (área o empleado), no amerita un morphTo genérico. Si
// alcance='empresa' no se inserta ninguna fila para ese decreto.
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sgrh_vacacion_colectiva_alcances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('vacacion_colectiva_id')->constrained('sgrh_vacacion_colectivas')->cascadeOnDelete();
            $table->enum('tipo', ['area', 'empleado']);
            // sgrh_areas.id o sgrh_empleados.id según 'tipo' — sin FK real porque apunta a dos
            // tablas distintas según el valor de 'tipo'.
            $table->unsignedBigInteger('referencia_id');
            $table->timestamps();

            $table->index(['tipo', 'referencia_id']);
            $table->index('vacacion_colectiva_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sgrh_vacacion_colectiva_alcances');
    }
};
