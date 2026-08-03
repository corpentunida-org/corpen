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
        Schema::create('rsv_transacciones_financieras', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_rsv_reservas')->constrained('rsv_reservas')->restrictOnDelete();

            $table->decimal('monto', 10, 2);
            $table->string('moneda', 3);
            $table->string('estado_pago', 50);

            $table->foreignId('id_rsv_pasarela')->constrained('rsv_pasarelas')->restrictOnDelete();

            $table->string('metodo_pago', 100);
            $table->string('referencia_externa', 255)->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rsv_transacciones_financieras');
    }
};
