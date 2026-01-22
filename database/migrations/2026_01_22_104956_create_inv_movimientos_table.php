<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('inv_movimientos', function (Blueprint $table) {
            $table->id();
            
            // Datos del Documento
            $table->string('codigo_acta')->unique(); // Ej: "ACT-2026-001" (No pueden repetirse)
            $table->string('acta_archivo')->nullable(); // La ruta donde se guarda el PDF
            $table->text('observacion_general')->nullable(); // "Se entrega equipo nuevo..."
            
            // Relaciones (Foreign Keys)
            
            // 1. ¿Qué tipo de movimiento es? (Asignación, Devolución, Préstamo)
            // Conecta con la tabla que creamos en Fase 1
            $table->foreignId('id_estado')->constrained('inv_estados');
            
            // 2. ¿Quién recibe o devuelve el equipo? (El empleado)
            $table->foreignId('id_usersAsignado')->constrained('users');
            
            // 3. ¿Quién hace el registro en el sistema? (Tú o el de Soporte)
            $table->foreignId('id_usersRegistro')->constrained('users');
            
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('inv_movimientos');
    }
};
