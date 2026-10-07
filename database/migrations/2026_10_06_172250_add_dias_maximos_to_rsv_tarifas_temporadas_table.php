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
        Schema::table('rsv_tarifas_temporadas', function (Blueprint $table) {
            // Agregamos la columna dias_maximos (puede ser nullable si es opcional)
            $table->integer('dias_maximos')->nullable()->after('precio_minimo_reserva');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('rsv_tarifas_temporadas', function (Blueprint $table) {
            $table->dropColumn('dias_maximos');
        });
    }
};
