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
     */
    protected $fillable = [
        'nombre',
        'fecha_inicio',
        'fecha_fin',
        'estado',
        'observacion',
        'descripcion',
        'documento',
    ];

    /**
     * Campos que serán tratados como fechas por Eloquent.
     */
    protected $dates = [
        'fecha_inicio',
        'fecha_fin',
        'created_at',
        'updated_at',
    ];

    /**
     * Accesor para mostrar solo el nombre del archivo del contrato.
     */
    public function getNombreArchivoAttribute()
    {
        return $this->documento ? basename($this->documento) : null;
    }

    /**
     * Mutator para asegurar que el estado siempre esté en minúsculas.
     */
    public function setEstadoAttribute($value)
    {
        $this->attributes['estado'] = strtolower($value);
    }
}
