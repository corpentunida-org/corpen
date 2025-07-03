<?php

namespace App\Models\Creditos;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Congregacion extends Model
{
    use HasFactory; //hacer las consultas mas faciles
    protected $table = 'CRE_Congregaciones';

    protected $fillable = [
        'Codigo',
        'Nombre_templo',
        'Estado',
        'Clase',
        'Municipio',
        'Direccion',
        'Telefono',
        'Celular',
        'Dist',
        'Fecha_Ap',
        'Fecha_Cie',
        'Obser',
        'Pastor',
    ];

}
