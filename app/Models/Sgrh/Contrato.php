<?php

namespace App\Models\Sgrh;

use Illuminate\Database\Eloquent\Model;

class Contrato extends Model
{
    protected $table = 'sgrh_contratos';

    protected $fillable = [
        'empleado_id',
        // Fecha en que se redactó/suscribió el contrato — distinta de fecha_inicio (cuándo
        // empieza a regir) y de created_at (cuándo se guardó el registro en el sistema).
        'fecha_creacion_contrato',
        'tipo_contrato_id',
        'cargo_id',
        'fecha_inicio',
        'fecha_vencimiento',
        'fecha_terminacion_real',
        'estado',
        'salario_contrato',
        // Enlace externo al gestor documental de la empresa (no se sube/almacena el PDF en
        // S3 desde SGRH — el archivo ya vive aparte).
        'documento_url',
    ];

    protected $casts = [
        'fecha_creacion_contrato' => 'date',
        'fecha_inicio' => 'date',
        'fecha_vencimiento' => 'date',
        'fecha_terminacion_real' => 'date',
        'salario_contrato' => 'decimal:2',
    ];

    public function empleado()
    {
        return $this->belongsTo(Empleado::class, 'empleado_id');
    }

    public function tipoContrato()
    {
        return $this->belongsTo(TipoContrato::class, 'tipo_contrato_id');
    }

    public function cargo()
    {
        return $this->belongsTo(Cargo::class, 'cargo_id');
    }

    public function modificaciones()
    {
        return $this->hasMany(ContratoModificacion::class, 'contrato_id')->latest();
    }

    /**
     * "Vencido" siempre se calcula en vivo desde fecha_vencimiento, nunca se confía en el
     * campo `estado` guardado (puede quedarse en 'Activo' hasta que el comando programado
     * corra o alguien lo revise manualmente) — así las alertas nunca quedan desactualizadas.
     */
    public function getEstaVencidoAttribute(): bool
    {
        return $this->fecha_vencimiento !== null && $this->fecha_vencimiento->isPast();
    }
}
