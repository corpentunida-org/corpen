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
        Schema::create('car_sia_bloques', function (Blueprint $table) {
            $table->id();

            // Llave de negocio: El número que ya vienes usando en car_sia_operaciones
            $table->integer('numero_bloque')->unique()->comment('Identificador del lote compartido con las operaciones');

            // Relación con el periodo (Mes/Año)
            // Usamos restrict para evitar borrar un mes si ya tiene lotes cargados
            $table->foreignId('id_periodo')->constrained('car_sia_periodos')->onDelete('restrict');

            $table->string('descripcion', 150)->nullable()->comment('Nombre o detalle opcional del lote (ej. Lote DIAN)');
            $table->string('estado', 30)->default('PENDIENTE')->comment('Estado de liquidación del lote completo');

            $table->timestamps();
        });
    }
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('car_sia_bloques');
    }
};
