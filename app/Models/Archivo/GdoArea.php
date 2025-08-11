<?php

namespace App\Models\Archivo;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GdoArea extends Model
{
    use HasFactory;

    /**
     * Nombre de la tabla en la base de datos.
     */
    protected $table = 'gdo_area';

    /**
     * Campos que se pueden asignar masivamente.
     */
    protected $fillable = [
        'nombre',
        'descripcion',
        'estado',
    ];

    /**
     * Campos que serán tratados como fechas por Eloquent.
     */
    protected $dates = [
        'created_at',
        'updated_at',
    ];

    /**
     * Mutator para guardar el estado siempre en minúsculas.
     */
    public function setEstadoAttribute($value)
    {
        $this->attributes['estado'] = strtolower($value);
    }

    /**
     * Accessor para mostrar el nombre en formato capitalizado.
     */
    public function getNombreAttribute($value)
    {
        return ucwords($value);
    }
}
