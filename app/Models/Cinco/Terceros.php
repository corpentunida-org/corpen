<?php

namespace App\Models\Cinco;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Terceros extends Model
{
    use HasFactory;
    protected $table = 'CIN_Terceros';
    protected $fillable = [
        'Fec_Ing',
        'Fec_Minis',
        'Fec_Aport',
        'observacion',
        'verificado',
        'verificadousuario'
    ];

}
