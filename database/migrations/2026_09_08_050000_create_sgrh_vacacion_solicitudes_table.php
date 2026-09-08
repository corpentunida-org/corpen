<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// Solicitud individual de vacaciones (o generada automáticamente por un decreto de vacaciones
// colectivas obligatorias, ver 'tipo' y 'vacacion_colectiva_id'). dias_habiles queda como foto
// calculada al crear — no se recalcula si la política cambia después, igual criterio que el
// snapshot de sgrh_contrato_modificaciones.
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sgrh_vacacion_solicitudes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('empleado_id')->constrained('sgrh_empleados');
            $table->date('fecha_inicio');
            $table->date('fecha_fin');
            $table->decimal('dias_habiles', 5, 2);
            $table->enum('tipo', ['individual', 'colectiva_obligatoria'])->default('individual');
            $table->enum('estado', ['pendiente', 'aprobada', 'rechazada', 'cancelada'])->default('pendiente');
            $table->boolean('es_adelantada')->default(false);
            // Quién autorizó tomar días aún no causados — obligatorio si es_adelantada=true (ver
            // VacacionValidador). Distinto de aprobador_user_id: autorizar el adelanto es previo
            // a resolver la solicitud en sí.
            $table->foreignId('autorizada_por_rrhh_id')->nullable()->constrained('users');
            $table->foreignId('aprobador_user_id')->nullable()->constrained('users');
            // En qué calidad resolvió: jefe inmediato, director (equipo extendido), o RRHH
            // (cargo sin jefe, o caso especial) — ver VacacionAprobadorResolver::puedeResolver().
            $table->enum('rol_aprobador', ['jefe_inmediato', 'director', 'rrhh'])->nullable();
            $table->timestamp('fecha_resolucion')->nullable();
            $table->text('motivo_rechazo')->nullable();
            $table->foreignId('vacacion_colectiva_id')->nullable()->constrained('sgrh_vacacion_colectivas');
            $table->text('observaciones')->nullable();
            $table->timestamps();

            $table->index(['empleado_id', 'estado']);
            $table->index(['fecha_inicio', 'fecha_fin']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sgrh_vacacion_solicitudes');
    }
};
