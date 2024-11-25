<?php

namespace App\Models\Seguros;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SegAsegurado extends Model
{
    use HasFactory;
    protected $table = 'SEG_asegurados';

    public function tercero()
    {
        return $this->belongsTo(SegTercero::class, 'cedula', 'cedula');
    }
}
