<?php

namespace App\Models\Seguros;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SegBeneficios extends Model
{
    use HasFactory;

    protected $table = 'seg_beneficios';

    protected $fillable = [
        'cedulaAsegurado', 'poliza','porcentajeDescuento', 'valorDescuento','observaciones','valorpagaranterior'
    ];
    public function tercero()
    {
        return $this->belongsTo(SegTercero::class, 'cedulaAsegurado', 'cedula');
    }
    public function polizarel()
    {
        return $this->belongsTo(SegPoliza::class, 'poliza', 'id');
    }
}
