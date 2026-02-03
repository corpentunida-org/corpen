<?php

namespace App\Models\Archivo;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;
use App\Models\Interacciones\Interaction;

class GdoCargo extends Model
{
    use HasFactory;

    protected $table = 'gdo_cargo';

    protected $fillable = [
        'nombre_cargo',
        'salario_base',
        'jornada',
        'telefono_corporativo',
        'celular_corporativo',
        'ext_corporativo',
        'correo_corporativo',
        'gmail_corporativo',
        'manual_funciones',
        'GDO_area_id',
        'GDO_empleados_cedula',
        'estado',
        'observacion',
    ];

    protected $casts = [
        'estado' => 'boolean',
        'salario_base' => 'decimal:2',
    ];

    // --- Relaciones existentes ---

    public function gdoArea()
    {
        return $this->belongsTo(GdoArea::class, 'GDO_area_id', 'id');
    }

    public function empleado()
    {
        return $this->belongsTo(GdoEmpleado::class, 'GDO_empleados_cedula', 'cedula');
    }

    public function user()
    {
        return $this->belongsTo(\App\Models\User::class, 'correo_corporativo', 'email');
    }

    public function interacciones()
    {
        return $this->hasMany(Interaction::class, 'id_cargo', 'id');
    }


    /**
     * Obtiene las funciones asignadas a este cargo.
     */
    public function funciones()
    {
        return $this->belongsToMany(
            GdoFuncion::class,      // Modelo relacionado
            'gdo_funcion_cargo',    // Tabla pivot
            'gdo_cargo_id',         // Llave foránea de este modelo en la pivot
            'gdo_funcion_id'        // Llave foránea del modelo funciones en la pivot
        )->withPivot('id', 'estado') // Traer campos extras de la tabla pivot
         ->withTimestamps();
    }
}