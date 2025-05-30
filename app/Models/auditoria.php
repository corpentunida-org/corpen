<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class auditoria extends Model
{
    protected $table = 'Auditoria';
    use HasFactory;
    public $timestamps = false;

    protected $fillable = [
        'fechaRegistro',
        'horaRegistro',
        'usuario',
        'usuario_id',
        'accion',
        'area'
    ];
}
