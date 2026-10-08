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
        Schema::table('rsv_transacciones_financieras', function (Blueprint $table) {
            // Agregamos el campo soporte_pago despues de referencia_externa (o donde prefieras)
            $table->string('soporte_pago', 255)->nullable()->after('referencia_externa');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('rsv_transacciones_financieras', function (Blueprint $table) {
            $table->dropColumn('soporte_pago');
        });
    }
};
