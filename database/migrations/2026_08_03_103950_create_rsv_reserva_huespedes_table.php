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
        Schema::create('rsv_reserva_huespedes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_rsv_reservas')->constrained('rsv_reservas')->cascadeOnDelete();
            $table->foreignId('id_user_registrador')->constrained('users')->restrictOnDelete();

            $table->string('nombre', 150);
            $table->string('apellidos', 150);
            $table->string('tipo_documento', 50);
            $table->string('numero_documento', 50);
            $table->boolean('es_titular')->default(false);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rsv_reserva_huespedes');
    }
};
