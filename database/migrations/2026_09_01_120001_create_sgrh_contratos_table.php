<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sgrh_contratos', function (Blueprint $table) {
            $table->id();
            // Sin nullOnDelete/cascadeOnDelete a propósito: es dato de auditoría histórico, no
            // debe poder desaparecer si algún día se borra un empleado (hoy tampoco existe un
            // destroy() de Empleado, así que es una protección barata).
            $table->foreignId('empleado_id')->constrained('sgrh_empleados');
            // Sin nullOnDelete: tipo_contrato_id es obligatorio (NOT NULL), así que se deja el
            // RESTRICT por defecto — no se puede borrar un tipo de contrato que esté en uso.
            $table->foreignId('tipo_contrato_id')->constrained('sgrh_tipos_contrato');
            $table->foreignId('cargo_id')->nullable()->constrained('sgrh_cargos')->nullOnDelete();
            $table->date('fecha_inicio');
            $table->date('fecha_vencimiento')->nullable();
            // Fecha real de cierre cuando difiere de fecha_vencimiento (terminación anticipada);
            // vacía mientras el contrato sigue vigente.
            $table->date('fecha_terminacion_real')->nullable();
            // Valores esperados en la app, sin enum de MySQL (mismo criterio que
            // sgrh_empleados.estado): Activo, Vencido, Liquidado, Renovado.
            $table->string('estado')->default('Activo');
            // Salario propio de este periodo contractual, distinto del salario_asignado único y
            // vigente que ya tiene Empleado — permite reconstruir el salario histórico exacto.
            $table->decimal('salario_contrato', 12, 2)->nullable();
            // Ruta relativa en el disco 's3' (no URL completa), mismo criterio que
            // GdoDocsEmpleados.ruta_archivo.
            $table->string('documento_path')->nullable();
            $table->text('observaciones')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sgrh_contratos');
    }
};
