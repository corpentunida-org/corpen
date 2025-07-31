<?php

namespace App\Models\Cinco;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Terceros extends Model
{
    use HasFactory;
    protected $table = 'CIN_Terceros';
    protected $fillable = [
        'Cod_Ter',
        'Nom_Ter',
        'Fec_Ing',
        'Fec_Minis',
        'Fec_Aport',
        'observacion',
        'verificado',
        'verificadousuario'
    ];

    public function movContable()
    {
        return $this->hasMany(MoviContCinco::class, 'Cedula', 'Cod_Ter');
    }

}
