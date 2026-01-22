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
        Schema::create('inv_movimientos_detalle', function (Blueprint $table) {
            $table->id();
            
            // Comentarios específicos del estado físico
            $table->string('estado_individual')->nullable(); // Ej: "El mouse se entrega sucio"
            
            // 1. RELACIÓN PADRE (VITAL): Conecta con la cabecera del Acta
            // onDelete('cascade') significa: Si borras el Acta, se borra esta lista automáticamente.
            $table->foreignId('id_InvMovimientos')
                ->constrained('inv_movimientos')
                ->onDelete('cascade');
            
            // 2. EL ACTIVO: ¿Qué equipo se está moviendo?
            $table->foreignId('id_InvActivos')->constrained('inv_activos');

            // 3. TIPO DE MOVIMIENTO: (Asignación, Devolución, etc.)
            // Conecta con la tabla de estados que creamos al inicio
            $table->foreignId('id_estado')->constrained('inv_estados');

            // 4. EL USUARIO: ¿Quién tiene el equipo ahora?
            $table->foreignId('id_usersDelActivo')->constrained('users');
            
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('inv_movimientos_detalle');
    }
};
