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

    // Área a la que pertenece este cargo
    public function gdoArea()
    {
        return $this->belongsTo(GdoArea::class, 'GDO_area_id', 'id');
    }

    public function empleado()
    {
        // belongsTo(ClaseRelacionada, campo_foraneo_en_gdo_cargo, campo_local_en_gdo_empleados)
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



}
