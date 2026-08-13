<?php

namespace App\Models\Integraciones;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LogIntegracion extends Model
{
    use HasFactory;

    // Nombre exacto de la tabla en la base de datos
    protected $table = 'log_integraciones';

    // Campos que permitimos guardar masivamente
    protected $fillable = [
        'nombre_api',
        'endpoint',
        'metodo',
        'codigo_respuesta',
        'tiempo_respuesta_ms',
        'estado',
        'mensaje_error',
    ];
}
