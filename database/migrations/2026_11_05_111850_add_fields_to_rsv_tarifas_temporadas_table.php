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
            // Agregamos el campo entero (puede ser nullable para evitar errores con registros viejos)
            $table->integer('max_reservas_anuales_cliente')->nullable()->after('dias_maximos');
            
            // Agregamos la columna 'deleted_at'
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('rsv_tarifas_temporadas', function (Blueprint $table) {
            $table->dropColumn('max_reservas_anuales_cliente');
            $table->dropSoftDeletes();
        });
    }
};