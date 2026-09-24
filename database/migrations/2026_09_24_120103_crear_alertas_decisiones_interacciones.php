<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Registra, por usuario y día calendario, si respondió o pospuso cuando se le forzó a
     * decidir sobre sus interacciones vencidas (aviso cada 3 horas). Una fila por día
     * (UNIQUE user_id+fecha) — si en el mismo día contesta "responder" en cualquier momento,
     * ese día ya cuenta como atendido aunque antes hubiera pospuesto; solo cuenta como
     * "pospuesto" si en todo el día nunca respondió. Sirve para detectar 3 días seguidos
     * posponiendo y avisar al admon del área (ver AlertasInteraccionesService).
     */
    public function up(): void
    {
        Schema::create('int_alerta_decisiones', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->date('fecha');
            $table->enum('decision', ['responder', 'posponer']);
            $table->timestamps();

            $table->unique(['user_id', 'fecha']);
        });

        // Log de escalaciones ya enviadas al admon del área, para no reenviar el mismo aviso
        // cada vez que la persona sigue posponiendo el mismo día (o en múltiplos de 3 días).
        Schema::create('int_alerta_escalaciones', function (Blueprint $table) {
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
        Schema::dropIfExists('int_alerta_escalaciones');
        Schema::dropIfExists('int_alerta_decisiones');
    }
};
