<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * MaeTerceros.congrega es la columna que conecta un pastor con su congregación
     * (MaeCongregacionController::maeTercero(), usado en todo el módulo de Congregaciones desde
     * hace meses) y nunca tuvo índice — con 26.207 filas, cualquier búsqueda o join por esa
     * columna sin índice puede colgarse (se confirmó en vivo: un filtro nuevo de "buscar
     * congregación por pastor" quedó más de 3 minutos sin responder antes de este fix).
     */
    public function up(): void
    {
        Schema::table('MaeTerceros', function (Blueprint $table) {
            $table->index('congrega');
        });
    }

    public function down(): void
    {
        Schema::table('MaeTerceros', function (Blueprint $table) {
            $table->dropIndex(['congrega']);
        });
    }
};
