<?php

namespace App\Models\Creditos;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CreFabrica extends Model
{
    public $timestamps = false;
    use HasFactory;

    protected $table = 'CRE_fabrica';

    protected $fillable = [
        'nombre',
        'cuenta',
        'tipo',
        'acuerdo',
        'tasa_interes',
        'plazo_maximo',
        'plazo_minimo',
        'edad_minima',
        'edad_maxima',
        'fecha_apertura',
        'fecha_cierre',
        'observacion',
        'id_garantia',
    ];

    protected $casts = [
        'id_garantia' => 'array',
    ];
}
