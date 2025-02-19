<?php

namespace App\Models\Seguros;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SegEstadoReclamacion extends Model
{
    use HasFactory;
    protected $table = 'SEG_estadoReclamacion';

    public function poliza()
    {
        return $this->belongsTo(SegPoliza::class, 'reclamacion', 'id');
    }

    public function reclamacion()
    {
        return $this->hasMany(SegReclamaciones::class, 'estado', 'id');
    }
    
}