<?php

namespace App\Models\Correspondencia;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Estado extends Model
{
    use HasFactory;

    protected $table = 'corr_estados';

    protected $fillable = [
        'nombre', 
        'descripcion',
        'activo', // Recomendado añadirlo si quieres desactivar estados sin borrarlos
    ];

    /**
     * Relación con las correspondencias que actualmente están en este estado.
     */
    public function correspondencias()
    {
        return $this->hasMany(Correspondencia::class, 'estado_id');
    }

    /**
     * Relación con la tabla pivot que configuramos en los pasos del flujo.
     * Esto permite saber en qué procesos/pasos se permite usar este estado.
     */
    public function procesosVinculados()
    {
        return $this->hasMany(EstadoProceso::class, 'id_estado');
    }

    /**
     * Relación directa con los Procesos (a través de la tabla de configuración).
     * Útil para consultas como: "Traer todos los pasos donde se permite 'Rechazar'"
     */
    public function procesos()
    {
        return $this->belongsToMany(Proceso::class, 'corr_estados_procesos', 'id_estado', 'id_proceso');
    }
}