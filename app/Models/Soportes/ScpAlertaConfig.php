<?php

namespace App\Models\Soportes;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class ScpAlertaConfig extends Model
{
    protected $table = 'scp_alerta_config';

    protected $fillable = ['aviso_intervalo_horas', 'pulso_intervalo_minutos', 'dias_posponer_para_escalar', 'updated_by'];

    protected $casts = [
        'aviso_intervalo_horas' => 'integer',
        'pulso_intervalo_minutos' => 'integer',
        'dias_posponer_para_escalar' => 'integer',
    ];

    public function actualizadoPor()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public static function actual(): self
    {
        return Cache::remember('scp_alerta_config', 3600, function () {
            return self::first() ?? new self([
                'aviso_intervalo_horas' => 3,
                'pulso_intervalo_minutos' => 10,
                'dias_posponer_para_escalar' => 3,
            ]);
        });
    }

    public static function olvidarCache(): void
    {
        Cache::forget('scp_alerta_config');
    }
}
