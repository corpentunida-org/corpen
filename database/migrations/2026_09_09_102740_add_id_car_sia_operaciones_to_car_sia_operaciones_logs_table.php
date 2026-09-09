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
        Schema::table('car_sia_operaciones_logs', function (Blueprint $table) {
            // Añade el campo después de numero_bloque para mantener el orden lógico
            $table->unsignedBigInteger('id_car_sia_operaciones')->nullable()->after('numero_bloque');

            // Foreign Key Constraint
            $table->foreign('id_car_sia_operaciones', 'fk_log_operacion_principal')
                  ->references('id')->on('car_sia_operaciones')
                  ->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('car_sia_operaciones_logs', function (Blueprint $table) {
            // Se elimina primero la relación foránea y luego la columna
            $table->dropForeign('fk_log_operacion_principal');
            $table->dropColumn('id_car_sia_operaciones');
        });
    }
};
