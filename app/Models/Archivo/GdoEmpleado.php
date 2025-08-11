<?php

namespace App\Models\Archivo;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GdoEmpleado extends Model
{
    use HasFactory;

    /**
     * Nombre de la tabla en la base de datos.
     */
    protected $table = 'gdo_empleados';

    /**
     * Campos que se pueden asignar masivamente.
     */
    protected $fillable = [
        'cedula',
        'apellido1',
        'apellido2',
        'nombre1',
        'nombre2',
        'nacimiento',
        'lugar',
        'sexo',
        'correo_personal',
        'celular_personal',
        'celular_acudiente',
    ];

    /**
     * Campos que serán tratados como fechas por Eloquent.
     */
    protected $dates = [
        'nacimiento',
        'created_at',
        'updated_at',
    ];

    /**
     * Accessor para obtener el nombre completo.
     */
    public function getNombreCompletoAttribute()
    {
        return trim("{$this->nombre1} {$this->nombre2} {$this->apellido1} {$this->apellido2}");
    }

    /**
     * Mutator para almacenar la cédula sin espacios.
     */
    public function setCedulaAttribute($value)
    {
        $this->attributes['cedula'] = preg_replace('/\s+/', '', $value);
    }

    /**
     * Mutator para guardar el correo en minúsculas.
     */
    public function setCorreoPersonalAttribute($value)
    {
        $this->attributes['correo_personal'] = strtolower($value);
    }
}
