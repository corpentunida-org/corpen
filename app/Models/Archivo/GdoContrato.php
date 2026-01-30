<?php

namespace App\Models\Archivo;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GdoContrato extends Model
{
    use HasFactory;

    /**
     * Nombre de la tabla en la base de datos.
     */
    protected $table = 'gdo_contrato';

    /**
     * Campos que se pueden asignar masivamente.
     * Se han actualizado según la nueva estructura de la imagen.
     */
    protected $fillable = [
        'valorSalarial',
        'fecha_inicio',
        'fecha_fin',
        'descripcion',
        'observacion',
        'tipoContrato',
        'estado',
    ];

    /**
     * Los atributos que deben ser convertidos a tipos nativos.
     * En versiones recientes de Laravel (8+), se prefiere $casts sobre $dates.
     */
    protected $casts = [
        'fecha_inicio' => 'date',
        'fecha_fin'    => 'date',
        'created_at'   => 'datetime',
        'updated_at'   => 'datetime',
    ];

    /**
     * Mutator para asegurar que el estado siempre esté en minúsculas.
     * Útil dado que tu base de datos usa un ENUM.
     */
    public function setEstadoAttribute($value)
    {
        $this->attributes['estado'] = strtolower($value);
    }
    
    /**
     * Si necesitas formatear el valor salarial como moneda (opcional)
     */
    public function getValorSalarialFormateadoAttribute()
    {
        return '$' . number_format((float)$this->valorSalarial, 2);
    }
}