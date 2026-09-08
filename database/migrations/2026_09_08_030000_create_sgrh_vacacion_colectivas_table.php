<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// Decretos de RRHH sobre un rango de fechas: 'obligatoria' genera automáticamente una
// solicitud ya aprobada para cada empleado del alcance elegido (descuenta su saldo sin que
// tengan que solicitarla); 'bloqueo' impide que cualquier empleado del alcance solicite
// vacaciones individuales que se solapen con esas fechas (ver VacacionValidador).
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sgrh_vacacion_colectivas', function (Blueprint $table) {
            $table->id();
            $table->enum('tipo', ['obligatoria', 'bloqueo']);
            $table->date('fecha_inicio');
            $table->date('fecha_fin');
            $table->text('descripcion');
            $table->enum('alcance', ['empresa', 'area', 'empleados']);
            // 'anulada' en vez de borrado físico: si es obligatoria, sus solicitudes generadas
            // (sgrh_vacacion_solicitudes.vacacion_colectiva_id) quedan como evidencia histórica
            // de qué decreto las originó, incluso si el decreto se anula después.
            $table->enum('estado', ['activa', 'anulada'])->default('activa');
            $table->foreignId('user_id')->constrained('users');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sgrh_vacacion_colectivas');
    }
};
