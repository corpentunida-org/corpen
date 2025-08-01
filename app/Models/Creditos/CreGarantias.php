<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CreGarantia extends Model
{
    use HasFactory;

    // Nombre de la tabla en la base de datos
    protected $table = 'CRE_garantias';

    // Campos que se pueden asignar masivamente
    protected $fillable = [
        'cuenta',
        'nombre',
        'descripcion',
        'documentacion',
    ];
}
