<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Agentes exentos de las alertas forzadas — compartida entre Interacciones (Daytrack) y
     * Soportes (no una tabla por módulo), pero separada por "tipo":
     *  - 'correo':   no recibe el correo diario de vencidas (Interacciones es el único que hoy
     *                le envía correo directo al propio agente; Soportes no tiene ese correo, así
     *                que este tipo por ahora solo tiene efecto ahí, queda listo si algún día
     *                Soportes también manda uno).
     *  - 'pantalla': no se le fuerza el modal de "Responder Ahora/Posponer" cada tantas horas
     *                (ni el de Interacciones ni el de Soportes) — igual puede seguir revisando
     *                sus propias alertas en la campanita si quiere, solo no lo interrumpe.
     * No afecta los correos que van A LOS ADMIN (informe semanal, inactividad, escalación) —
     * esos siguen mostrando a todo el mundo, es información de supervisión, no un aviso al
     * propio agente.
     */
    public function up(): void
    {
        Schema::create('int_alerta_omitidos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->enum('tipo', ['correo', 'pantalla']);
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->unique(['user_id', 'tipo']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('int_alerta_omitidos');
    }
};
