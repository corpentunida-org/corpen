<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * "N° Cuota" pasa a ser un rango (Desde/Hasta) para poder registrar en un solo comprobante
     * un pago que cubre varias cuotas seguidas — numero_cuota ya existente sigue siendo el
     * "desde" (no se renombra, para no romper el buscador/reportes que ya lo usan); esta
     * columna nueva es el "hasta". Nullable y aditiva: los registros viejos quedan sin "hasta"
     * (se interpretan como una sola cuota, igual que hasta ahora).
     */
    public function up(): void
    {
        Schema::table('car_comprobantes_pagos', function (Blueprint $table) {
            $table->integer('hasta_cuota')->nullable()->after('numero_cuota');
        });
    }

    public function down(): void
    {
        Schema::table('car_comprobantes_pagos', function (Blueprint $table) {
            $table->dropColumn('hasta_cuota');
        });
    }
};
