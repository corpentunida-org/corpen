<?php

namespace App\Models\Seguros;

use App\Models\Maestras\maeTerceros;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SegNovedades extends Model
{
    use HasFactory;
    protected $table = 'Seg_novedadaes';
    protected $fillable = [
        'id_poliza',
        'id_asegurado',
        'retiro',
        'plan',
        'valorpagar',
        'valorAsegurado',
        'fechaNovedad',
        'observaciones',
        'valorPrimaPlan'
    ];

    /* public function tercero()
    {
        return $this->belongsTo(SegTercero::class, 'id_asegurado', 'cedula');
    } */
    public function tercero()
    {
        return $this->belongsTo(maeTerceros::class, 'id_asegurado', 'cod_ter');
    }

    public function asegurado()
    {
        return $this->belongsTo(SegAsegurado::class, 'id_asegurado', 'cedula');
    }
    public function plan()
    {
        return $this->belongsTo(SegPlan::class, 'planNuevo', 'id');
    }
}
