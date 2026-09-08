<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// Correcciones manuales de RRHH al saldo de un colaborador (ej. reconocimiento de días,
// corrección de una migración de datos) — lo único del saldo que no se deriva de una fórmula
// (antigüedad + política vigente - solicitudes aprobadas), por eso es la única pieza que se
// persiste; el resto se calcula siempre en vivo (ver VacacionSaldoCalculador).
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sgrh_vacacion_saldo_ajustes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('empleado_id')->constrained('sgrh_empleados');
            $table->decimal('dias', 6, 2); // positivo suma al saldo, negativo resta
            $table->string('motivo');
            $table->foreignId('user_id')->constrained('users');
            $table->timestamps();

            $table->index('empleado_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sgrh_vacacion_saldo_ajustes');
    }
};
