<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('sgrh_empleados', function (Blueprint $table) {
            $table->id();

            // Enlace de negocio a MaeTerceros.cod_ter (tabla legada, sin migración Laravel,
            // por eso no se usa foreignId()->constrained()).
            $table->unsignedBigInteger('cod_ter')->unique();

            $table->date('fecha_ingreso')->nullable();
            $table->string('estado')->default('activo'); // activo | inactivo | retirado
            $table->date('fecha_retiro')->nullable();

            $table->string('eps')->nullable();
            $table->string('arl')->nullable();
            $table->string('fondo_pension')->nullable();
            $table->string('tipo_sangre', 5)->nullable();

            $table->string('contacto_emergencia_nombre')->nullable();
            $table->string('contacto_emergencia_telefono', 20)->nullable();

            $table->string('foto_perfil')->nullable(); // ruta en S3

            $table->text('observaciones')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sgrh_empleados');
    }
};
