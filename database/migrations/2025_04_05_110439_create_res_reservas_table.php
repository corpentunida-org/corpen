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
        Schema::create('res_reservas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('res_inmueble_id')->constrained('res_inmuebles')->onDelete('cascade');
            $table->foreignId('res_status_id')->constrained('res_statuses')->onDelete('cascade');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->string('nid');
            $table->date('fecha_solicitud');
            $table->date('fecha_inicio');
            $table->date('fecha_fin');
            $table->text('retroalimentacion')->nullable();
            $table->date('fecha_retroalimentacion')->nullable();
            $table->double('puntuacion')->default(0);
            $table->text('observacion_recibo')->nullable();
            $table->date('fecha_recibo')->nullable();
            $table->integer('user_id_recibo')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('res_reservas');
    }
};
