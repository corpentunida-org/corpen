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
        Schema::create('rsv_reservas', function (Blueprint $table) {
            $table->id();

            // Agregado a partir del diagrama visual (Pág 7), omitido en tipos de datos (Pág 16)
            $table->string('codigo_reserva', 50)->unique();

            $table->foreignId('id_rsv_catalogo_inmueble')->constrained('rsv_catalogo_inmueble')->restrictOnDelete();
            $table->foreignId('id_user')->constrained('users')->restrictOnDelete();
            $table->foreignId('id_rsv_statuses')->constrained('rsv_statuses')->restrictOnDelete();

            $table->timestamp('fecha_inicio')->nullable();
            $table->timestamp('fecha_fin')->nullable();

            $table->decimal('monto_total', 10, 2);
            $table->foreignId('id_rsv_origen_reservas')->constrained('rsv_origen_reservas')->restrictOnDelete();
            $table->text('comentario_reserva')->nullable();

            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rsv_reservas');
    }
};
