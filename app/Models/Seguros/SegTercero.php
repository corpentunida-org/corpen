<?php

namespace App\Models\Seguros;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SegTercero extends Model
{
    use HasFactory;

    protected $table = 'SEG_terceros';
    public function asegurados()
    {
        return $this->hasMany(SegAsegurado::class, 'cedula', 'cedula');
    }
    public function polizas()
    {
        return $this->hasMany(SegPoliza::class, 'seg_asegurado_id');
    }
}
