<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Mismo mecanismo que int_alerta_decisiones/int_alerta_escalaciones (ver esas migraciones),
     * replicado para Soportes: cuando el aviso forzado (soportes asignados a mí, sin cerrar) pide
     * decidir, se registra aquí si respondió o pospuso ese día. 3 (configurable) días seguidos
     * posponiendo sin responder ninguno → se avisa a superadmin/admindesarrollo (Soportes no
     * tiene una estructura de "admon por área" como Interacciones, ver conversación).
     */
    public function up(): void
    {
        Schema::create('scp_alerta_decisiones', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->date('fecha');
            $table->enum('decision', ['responder', 'posponer']);
            $table->timestamps();

            $table->unique(['user_id', 'fecha']);
        });

        Schema::create('scp_alerta_escalaciones', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->date('fecha');
            $table->unsignedInteger('dias_consecutivos');
            $table->timestamps();

            $table->unique(['user_id', 'fecha']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('scp_alerta_escalaciones');
        Schema::dropIfExists('scp_alerta_decisiones');
    }
};
