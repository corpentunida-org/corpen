<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Configuración del aviso forzado de Soportes (una sola fila, mismo patrón que
     * int_alerta_config). Soportes no tiene correos programados diarios/semanales como
     * Interacciones, así que solo trae el intervalo del aviso y el umbral de escalación.
     */
    public function up(): void
    {
        Schema::create('scp_alerta_config', function (Blueprint $table) {
            $table->id();
            $table->unsignedTinyInteger('aviso_intervalo_horas')->default(3);
            $table->unsignedTinyInteger('dias_posponer_para_escalar')->default(3);
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });

        DB::table('scp_alerta_config')->insert([
            'aviso_intervalo_horas' => 3,
            'dias_posponer_para_escalar' => 3,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('scp_alerta_config');
    }
};
