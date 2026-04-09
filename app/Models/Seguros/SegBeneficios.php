<?php

namespace App\Models\Seguros;

use App\Models\Maestras\MaeTerceros;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SegBeneficios extends Model
{
    use HasFactory;

    protected $table = 'seg_beneficios';

    protected $fillable = ['cedulaAsegurado', 'poliza', 'porcentajeDescuento', 'valorDescuento', 'observaciones', 'valorpagaranterior', 'active', 'fechaFin'];
    /* public function tercero()
    {
        return $this->belongsTo(SegTercero::class, 'cedulaAsegurado', 'cedula');
    } */

    public function tercero()
    {
        return $this->belongsTo(MaeTerceros::class, 'cedulaAsegurado', 'cod_ter');
    }

    public function polizarel()
    {
        return $this->belongsTo(SegPoliza::class, 'poliza', 'id');
    }
}
