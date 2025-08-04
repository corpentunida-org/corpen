<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('GDO_cargo', function (Blueprint $table) {
            $table->id(); // Clave primaria autoincremental

            // Información principal del cargo
            $table->string('nombre_cargo', 255)->nullable(); // Nombre del cargo (Ej: Coordinador de área)
            $table->decimal('salario_base', 12, 2)->nullable(); // Salario base mensual (valor numérico)
            $table->string('jornada', 100)->nullable(); // Tipo de jornada laboral (Ej: Tiempo completo, medio tiempo)

            // Contacto corporativo asociado al cargo
            $table->string('telefono_corporativo', 50)->nullable(); // Teléfono corporativo fijo
            $table->string('celular_corporativo', 50)->nullable(); // Celular corporativo
            $table->string('ext_corporativo', 20)->nullable(); // Extensión telefónica interna
            $table->string('correo_corporativo', 255)->nullable(); // Correo electrónico institucional
            $table->string('gmail_corporativo', 255)->nullable(); // Gmail asociado al cargo (si aplica)

            // Otros datos asociados al cargo
            $table->string('manual_funciones', 255)->nullable(); // Ruta o nombre del archivo PDF con el manual de funciones
            $table->string('empleado_cedula', 50)->nullable(); // Cédula del empleado asignado (relación futura)

            // Estado y observaciones
            $table->boolean('estado')->default(true); // Estado del cargo: true = Activo, false = Inactivo
            $table->text('observacion')->nullable(); // Observaciones generales o comentarios sobre el cargo

            $table->timestamps(); // Fechas de creación y última actualización
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('GDO_cargo');
    }
};
