<?php

namespace App\Models\Archivo;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cargo extends Model
{
    use HasFactory;

    protected $table = 'GDO_cargo';

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
        'empleado_cedula',
        'estado',
        'observacion',
    ];

    protected $casts = [
        'estado' => 'boolean',
        'salario_base' => 'decimal:2',
    ];
}
