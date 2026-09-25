<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Motivo de "No Efectivo" — mismo nivel que "outcome": vive en interactions (el estado
     * actual) y también en int_seguimiento (el historial, un motivo por cada gestión). Nullable
     * porque solo aplica cuando el resultado elegido es "No Efectivo" — para cualquier otro
     * resultado queda vacío.
     */
    public function up(): void
    {
        Schema::table('interactions', function (Blueprint $table) {
            $table->unsignedBigInteger('motivo_no_efectivo_id')->nullable()->after('outcome');
            $table->foreign('motivo_no_efectivo_id')->references('id')->on('int_motivos_no_efectivo')->nullOnDelete();
        });

        Schema::table('int_seguimiento', function (Blueprint $table) {
            $table->unsignedBigInteger('motivo_no_efectivo_id')->nullable()->after('outcome');
            $table->foreign('motivo_no_efectivo_id')->references('id')->on('int_motivos_no_efectivo')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('interactions', function (Blueprint $table) {
            $table->dropForeign(['motivo_no_efectivo_id']);
            $table->dropColumn('motivo_no_efectivo_id');
        });

        Schema::table('int_seguimiento', function (Blueprint $table) {
            $table->dropForeign(['motivo_no_efectivo_id']);
            $table->dropColumn('motivo_no_efectivo_id');
        });
    }
};
