<?php

namespace App\Models\Correspondencia;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MedioRecepcion extends Model
{
    use HasFactory;

    /**
     * El nombre de la tabla asociada al modelo.
     * @var string
     */
    protected $table = 'corr_medio_recepcion';

    /**
     * Los atributos que se pueden asignar masivamente.
     * @var array
     */
    protected $fillable = [
        'id',
        'codigo',
        'nombre',
        'descripcion',
        'activo',
    ];

    /**
     * Los atributos que deben ser convertidos a tipos nativos.
     * @var array
     */
    protected $casts = [
        'activo' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Scope para filtrar solo los medios activos.
     * Uso: MedioRecepcion::activos()->get();
     */
    public function scopeActivos($query)
    {
        return $query->where('activo', true);
    }
}