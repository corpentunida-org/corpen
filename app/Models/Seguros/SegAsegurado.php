<?php

namespace App\Models\Seguros;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SegAsegurado extends Model
{
    use HasFactory;
    protected $table = 'SEG_asegurados';

    
    public function terceroAF()
    {
        return $this->belongsTo(SegTercero::class, 'titular', 'cedula');
    }

    public function tercero()
    {
        return $this->belongsTo(SegTercero::class, 'cedula', 'cedula');
    }

    public function polizas()
    {
        return $this->hasMany(SegPoliza::class, 'seg_asegurado_id','cedula');
    }

    public function reclamacion()
    {
        return $this->hasMany(SegReclamaciones::class, 'cedulaAsegurado','cedula');
    }
}