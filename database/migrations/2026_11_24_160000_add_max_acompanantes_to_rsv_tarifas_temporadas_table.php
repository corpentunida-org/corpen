<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Máximo de acompañantes (sin contar al titular) que admite una reserva
     * del inmueble en el año de la tarifa. NULL = sin límite propio: se usa
     * la capacidad del inmueble menos el titular.
     */
    public function up(): void
    {
        Schema::table('rsv_tarifas_temporadas', function (Blueprint $table) {
            $table->unsignedSmallInteger('max_acompanantes')->nullable()->after('max_reservas_anuales_cliente');
        });
    }

    public function down(): void
    {
        Schema::table('rsv_tarifas_temporadas', function (Blueprint $table) {
            $table->dropColumn('max_acompanantes');
        });
    }
};
