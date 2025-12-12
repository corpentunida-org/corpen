<?php

namespace App\Models\Exequiales;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ExServicioComentarios extends Model
{
    use HasFactory;
    protected $table = 'Exe_ServiciosComentarios';
    protected $fillable = ['id_exser', 'tipo', 'fecha', 'observacion'];

    
}
