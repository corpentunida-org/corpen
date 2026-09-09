<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Trazabilidad de reafiliación: se agregan los campos sobre el MISMO registro de
 * retiro (en vez de borrarlo o crear una tabla aparte) para que cada fila cuente
 * el ciclo completo — cuándo se retiró y, si aplica, cuándo y por qué volvió a
 * afiliarse. 'fecha_reafiliacion' nula significa que ese retiro sigue vigente.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('exe_titulares_retiros', function (Blueprint $table) {
            $table->date('fecha_reafiliacion')->nullable()->after('observaciones');
            $table->text('observacion_reafiliacion')->nullable()->after('fecha_reafiliacion');
            $table->foreignId('reafiliado_por')->nullable()->after('observacion_reafiliacion')
                ->constrained('users')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('exe_titulares_retiros', function (Blueprint $table) {
            $table->dropConstrainedForeignId('reafiliado_por');
            $table->dropColumn(['fecha_reafiliacion', 'observacion_reafiliacion']);
        });
    }
};
