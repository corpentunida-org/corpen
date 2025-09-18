<?php

namespace App\Models\Soportes;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ScpTipoObservacion extends Model
{
    use HasFactory;

    protected $table = 'scp_tipo_observacions'; // Nombre de la tabla en la BD

    protected $fillable = [
        'nombre',
    ];
}
