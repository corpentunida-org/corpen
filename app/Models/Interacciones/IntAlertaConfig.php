<?php

namespace App\Models\Interacciones;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class IntAlertaConfig extends Model
{
    protected $table = 'int_alerta_config';

    protected $fillable = [
        'aviso_intervalo_horas',
        'dias_posponer_para_escalar',
        'correo_diario_hora',
        'informe_semanal_dia',
        'informe_semanal_hora',
        'inactividad_hora',
        'updated_by',
    ];

    protected $casts = [
        'aviso_intervalo_horas' => 'integer',
        'dias_posponer_para_escalar' => 'integer',
        'informe_semanal_dia' => 'integer',
    ];

    public function actualizadoPor()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    /**
     * Fila única de configuración (con caché de 1h, se lee en cada carga de la campanita y en
     * cada corrida de los comandos programados) — si por algún motivo no existe la fila
     * (nunca debería pasar tras la migración), cae a los mismos valores que antes estaban fijos
     * en el código, para no dejar las alertas rotas.
     */
    public static function actual(): self
    {
        return Cache::remember('int_alerta_config', 3600, function () {
            return self::first() ?? new self([
                'aviso_intervalo_horas' => 3,
                'dias_posponer_para_escalar' => 3,
                'correo_diario_hora' => '08:00:00',
                'informe_semanal_dia' => 1,
                'informe_semanal_hora' => '07:00:00',
                'inactividad_hora' => '17:30:00',
            ]);
        });
    }

    public static function olvidarCache(): void
    {
        Cache::forget('int_alerta_config');
    }
}
