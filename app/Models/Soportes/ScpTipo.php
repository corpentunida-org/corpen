<?php

namespace App\Models\Soportes;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ScpTipo extends Model
{
    use HasFactory;

    protected $table = 'scp_tipos'; // Nombre de la tabla en la BD

    protected $fillable = [
        'nombre',
    ];
}
