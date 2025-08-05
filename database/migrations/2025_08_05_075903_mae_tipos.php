<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Ejecuta la migración.
     */
    public function up(): void
    {
        Schema::create('MaeTipos', function (Blueprint $table) {
            $table->id();

            // Datos principales
            $table->string('codigo')->unique()->comment('Código único del tipo, ej: PT_ACT');
            $table->string('nombre')->comment('Nombre del tipo, ej: Pastor Activo');
            $table->text('descripcion')->nullable()->comment('Descripción detallada del tipo');

            // Clasificación
            $table->string('grupo')->nullable()->comment('Grupo general del tipo, ej: Pastor o Empleado');
            $table->string('categoria')->nullable()->comment('Categoría temática o funcional');
            $table->integer('orden')->default(0)->comment('Orden visual o de prioridad');

            // Control de estado
            $table->boolean('activo')->default(true)->comment('Indica si el tipo está activo');
            $table->boolean('editable')->default(true)->comment('Indica si se puede editar');
            $table->boolean('eliminable')->default(true)->comment('Indica si se puede eliminar');

            // Auditoría
            $table->unsignedBigInteger('created_by')->nullable()->comment('ID del usuario que creó el registro');
            $table->unsignedBigInteger('updated_by')->nullable()->comment('ID del usuario que actualizó el registro');
            $table->softDeletes()->comment('Eliminación lógica del registro');

            $table->timestamps();

            // Relaciones
            $table->foreign('created_by')->references('id')->on('users')->nullOnDelete();
            $table->foreign('updated_by')->references('id')->on('users')->nullOnDelete();
        });
    }

    /**
     * Revierte la migración.
     */
    public function down(): void
    {
        Schema::dropIfExists('MaeTipos');
    }
};
