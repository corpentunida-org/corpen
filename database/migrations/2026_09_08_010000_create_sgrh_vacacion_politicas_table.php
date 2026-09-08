<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// Política de vacaciones versionada: cada cambio es una fila nueva, nunca se pisa una vigente
// (mismo criterio que sgrh_contrato_modificaciones) — así el saldo de una fecha pasada sigue
// calculándose con la regla que aplicaba entonces, aunque RRHH cambie los días/año hoy.
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sgrh_vacacion_politicas', function (Blueprint $table) {
            $table->id();
            $table->unsignedTinyInteger('dias_por_anio')->default(15);
            $table->enum('tipo_dias', ['habiles', 'calendario'])->default('habiles');
            // null = sin tope de acumulación (el saldo puede crecer indefinidamente si no se toma).
            $table->unsignedTinyInteger('max_dias_acumulables')->nullable();
            $table->boolean('permite_adelanto')->default(true);
            // Tope de saldo negativo permitido por adelanto; null = sin tope explícito (queda
            // sujeto solo a la aprobación puntual de RRHH en cada caso).
            $table->unsignedSmallInteger('max_dias_adelanto')->nullable();
            // Solo una fila debe tener activa=true a la vez — VacacionPoliticaController::store()
            // desactiva la anterior antes de insertar la nueva, nunca se hace update in-place.
            $table->boolean('activa')->default(true);
            $table->date('vigente_desde');
            $table->text('observaciones')->nullable();
            $table->foreignId('user_id')->constrained('users');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sgrh_vacacion_politicas');
    }
};
