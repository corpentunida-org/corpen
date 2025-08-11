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
        Schema::create('gdo_empleados', function (Blueprint $table) {
            $table->id();

            $table->string('cedula', 20)->unique(); // Documento de identidad

            $table->string('apellido1', 50);
            $table->string('apellido2', 50)->nullable();
            $table->string('nombre1', 50);
            $table->string('nombre2', 50)->nullable();

            $table->date('nacimiento'); // Fecha de nacimiento
            $table->string('lugar', 100)->nullable(); // Lugar de nacimiento
            $table->enum('sexo', ['M', 'F', 'Otro'])->nullable(); // Sexo

            $table->string('correo_personal', 150)->unique(); // Correo personal
            $table->string('celular_personal', 20)->nullable();
            $table->string('celular_acudiente', 20)->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('gdo_empleados');
    }
};


