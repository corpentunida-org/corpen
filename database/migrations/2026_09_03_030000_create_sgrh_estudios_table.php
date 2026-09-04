<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// Estudios/formación académica del colaborador. cascadeOnDelete a propósito (igual criterio
// que sgrh_dependientes): un estudio no tiene sentido sin el colaborador al que pertenece, no
// es dato de auditoría independiente como sgrh_contratos.
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sgrh_estudios', function (Blueprint $table) {
            $table->id();
            $table->foreignId('empleado_id')->constrained('sgrh_empleados')->cascadeOnDelete();
            $table->string('programa');
            $table->string('institucion_educativa')->nullable();
            // 'Formal' (programa académico titulado) o 'Informal' (educación para el trabajo:
            // cursos, diplomados, certificaciones cortas).
            $table->string('tipo_formacion', 20);
            // Catálogo fijo de nivel educativo (Bachiller, Técnico, Profesional, Maestría...)
            // — ver EstudioController::NIVELES_FORMACION.
            $table->string('nivel_formacion', 60);
            $table->boolean('graduado')->default(false);
            // Nullable: un estudio puede seguir en curso (sin fecha de terminación todavía).
            $table->date('fecha_terminacion')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sgrh_estudios');
    }
};
