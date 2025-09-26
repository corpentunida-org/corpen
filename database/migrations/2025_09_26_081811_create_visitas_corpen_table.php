<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('visitas_corpen', function (Blueprint $table) {
            $table->id();
            
            // BIGINT igual que cod_ter en MaeTerceros
            $table->bigInteger('cliente_id')->nullable();

            $table->string('tipo')->default('visita'); // visita, seguimiento, etc.
            $table->string('banco')->nullable();
            $table->text('motivo')->nullable();
            $table->dateTime('fecha')->default(now());
            $table->string('registrado_por')->nullable(); // Usuario que registró la visita

            $table->timestamps();

            // Clave foránea
            $table->foreign('cliente_id')
                  ->references('cod_ter')
                  ->on('MaeTerceros')
                  ->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('visitas_corpen');
    }
};
