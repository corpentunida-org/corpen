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
            $table->decimal('precio_minimo_reserva', 10, 2)->after('precio_fin_semana');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('rsv_tarifas_temporadas', function (Blueprint $table) {
            $table->dropColumn('precio_minimo_reserva');
        });
    }
};
