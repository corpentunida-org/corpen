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
        Schema::create('inv_mantenimientos', function (Blueprint $table) {
            $table->id();
            
            // Información de la Reparación
            $table->text('detalle'); // Descripción larga del daño o arreglo
            $table->decimal('costo_mantenimiento', 15, 2); // Dinero exacto
            $table->string('acta')->nullable(); // Archivo PDF del técnico externo
            
            // Relaciones
            // 1. ¿Qué equipo se reparó?
            $table->foreignId('id_InvActivos')->constrained('inv_activos');
            
            // 2. ¿Quién registró esto en el sistema?
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
        Schema::dropIfExists('inv_mantenimientos');
    }
};
