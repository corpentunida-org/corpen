<?php

namespace App\Models\Sgrh;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;

class VacacionPolitica extends Model
{
    protected $table = 'sgrh_vacacion_politicas';

    protected $fillable = [
        'dias_por_anio',
        'tipo_dias',
        'max_dias_acumulables',
        'permite_adelanto',
        'max_dias_adelanto',
        'activa',
        'vigente_desde',
        'observaciones',
        'user_id',
    ];

    protected $casts = [
        'vigente_desde' => 'date',
        'permite_adelanto' => 'boolean',
        'activa' => 'boolean',
    ];

    public function usuario()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Política vigente en este momento — siempre la única fila con activa=true. El cálculo de
     * saldo (VacacionSaldoCalculador) nunca recalcula solicitudes ya resueltas con la política
     * anterior, solo usa esta para consultas en vivo.
     */
    public static function vigente(): ?self
    {
        return static::where('activa', true)->latest('vigente_desde')->first();
    }
}
