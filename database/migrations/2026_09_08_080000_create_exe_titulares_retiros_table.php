<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Registro de retiros de titulares de Exequiales. No reutiliza EXE_ExCli.estado
 * (ese boolean ya representa "fallecido" vía Prestar Servicio) porque mezclar los
 * dos motivos ahí haría imposible distinguir después "murió" de "se retiró".
 *
 * 'nombre' y 'fecha_afiliacion' se guardan como snapshot al momento del retiro en
 * vez de depender de un join en vivo: el nombre solo vive en la API externa (no
 * en EXE_ExCli), y este es un registro histórico que no debe cambiar si el dato
 * de origen cambia después.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('exe_titulares_retiros', function (Blueprint $table) {
            $table->id();
            $table->string('cod_cli', 20);
            $table->string('nombre')->nullable();
            $table->date('fecha_afiliacion')->nullable();
            $table->date('fecha_retiro');
            $table->text('observaciones')->nullable();
            $table->boolean('reportado_aliado')->default(false);
            $table->timestamp('reportado_en')->nullable();
            $table->foreignId('reportado_por')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->timestamps();

            $table->index('cod_cli');
            $table->index('fecha_retiro');
            $table->index('reportado_aliado');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('exe_titulares_retiros');
    }
};
