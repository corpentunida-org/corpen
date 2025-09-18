<?php

namespace App\Models\Soportes;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ScpEstado extends Model
{
    use HasFactory;

    protected $table = 'scp_estados'; // Nombre de la tabla en la BD

    protected $fillable = [
        'nombre',
        'descripcion',
        'color',
    ];
}
