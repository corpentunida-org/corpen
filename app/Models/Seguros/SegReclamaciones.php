<?php

namespace App\Models\Seguros;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SegReclamaciones extends Model
{
    use HasFactory;
    protected $table = 'SEG_reclamaciones';

    public function asegurado()
    {
        return $this->belongsTo(SegAsegurado::class, 'cedulaAsegurado','cedula');
    }

    
}
