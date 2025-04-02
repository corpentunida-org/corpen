<?php

namespace App\Models\Seguros;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SegCambioEstadoReclamacion extends Model
{
    use HasFactory;
    protected $table = 'SEG_CambioEstadoReclamacion';
    public $timestamps = false;

    protected $fillable = [
        'reclamacion_id',
        'estado_id',
        'observacion',
        'fecha_actualizacion',
        'hora_actualizacion',
    ];

    public function estado()
    {
        return $this->belongsTo(SegEstadoReclamacion::class, 'estado_id', 'id');
    }

}