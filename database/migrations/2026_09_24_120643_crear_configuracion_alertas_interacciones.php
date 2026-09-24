<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Configuración de las alertas de Interacciones (Daytrack) — una sola fila (singleton,
     * como el patrón ya usado en corpentunida_crm_configs). Antes todos estos números estaban
     * fijos en el código (3 horas, 3 días, 08:00, lunes 07:00, 17:30) — ahora se pueden ajustar
     * desde una pantalla sin tocar código. Ver App\Models\Interacciones\IntAlertaConfig::actual().
     */
    public function up(): void
    {
        Schema::create('int_alerta_config', function (Blueprint $table) {
            $table->id();
            $table->unsignedTinyInteger('aviso_intervalo_horas')->default(3);
            $table->unsignedTinyInteger('dias_posponer_para_escalar')->default(3);
            $table->time('correo_diario_hora')->default('08:00:00');
            $table->unsignedTinyInteger('informe_semanal_dia')->default(1); // 1=lunes ... 7=domingo (ISO)
            $table->time('informe_semanal_hora')->default('07:00:00');
            $table->time('inactividad_hora')->default('17:30:00');
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });

        // Fila inicial con los mismos valores que ya estaban fijos en el código, para que no
        // cambie el comportamiento hasta que alguien lo ajuste a propósito desde la pantalla.
        DB::table('int_alerta_config')->insert([
            'aviso_intervalo_horas' => 3,
            'dias_posponer_para_escalar' => 3,
            'correo_diario_hora' => '08:00:00',
            'informe_semanal_dia' => 1,
            'informe_semanal_hora' => '07:00:00',
            'inactividad_hora' => '17:30:00',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('int_alerta_config');
    }
};
