<?php

namespace App\Models\Archivo;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

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
        'empleado_cedula',
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


}
