<?php

namespace App\Models\Seguros;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SegNovedades extends Model
{
    use HasFactory;
    protected $table = 'Seg_novedadaes';
    protected $fillable = [
        'id_asegurado',
        'id_poliza',
        'valorpAseguradora',
        'planNuevo',
        'valoraPlan',
        'retiro',
        'fechaNovedad',
        'observaciones',
    ];
}
