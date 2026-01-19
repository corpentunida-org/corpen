<?php

namespace App\Models\Indicators;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class IndUsuarios extends Model
{
    use HasFactory;

    protected $table = 'Ind_usuarios';

    protected $fillable = ['id_correo', 'nombre', 'preguntas', 'respuestas', 'puntaje', 'fecha','prueba'];

    protected $casts = [
        'preguntas' => 'array',
        'respuestas' => 'array',
    ];
}
