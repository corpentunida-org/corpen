<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// Historial de modificaciones de contrato: un insert por cada edición (ContratoController::
// update()), con la causal que la motivó. Es dato de auditoría — igual criterio que
// sgrh_contratos.empleado_id (RESTRICT, no cascadeOnDelete): no debe poder desaparecer si el
// contrato al que pertenece se borra (hoy tampoco existe un destroy() de Contrato).
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sgrh_contrato_modificaciones', function (Blueprint $table) {
            $table->id();
            $table->foreignId('contrato_id')->constrained('sgrh_contratos');
            // Valores esperados en la app, sin enum de MySQL (mismo criterio que
            // sgrh_contratos.estado): 'Cambio de cargo o salario', 'Cambio de ubicación', 'Otra'.
            $table->string('causal');
            $table->text('observacion')->nullable();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sgrh_contrato_modificaciones');
    }
};
