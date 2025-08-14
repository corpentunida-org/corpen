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
        'fecha_expedida',
        'lugar_exp',
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
        'ubicacion_foto',
        'direccion_residencia',
        'entidad_eps',
                
    ];

    /**
     * Conversión automática de campos a tipos de dato.
     */
    protected $casts = [
        'nacimiento' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Relación con los documentos de empleado.
     */
    public function documentos()
    {
        return $this->hasMany(GdoDocsEmpleados::class, 'empleado_id', 'cedula');
    }


    /**
     * Relación con el cargo.
     */
    public function cargo()
    {
        // hasOne(ClaseRelacionada, campo_foraneo_en_gdo_cargo, campo_local_en_gdo_empleados)
        return $this->hasOne(GdoCargo::class, 'GDO_empleados_cedula', 'cedula');
    }

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
