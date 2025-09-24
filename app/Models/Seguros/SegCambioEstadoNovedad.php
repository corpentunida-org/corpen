<?php

namespace App\Models\Seguros;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SegCambioEstadoNovedad extends Model
{
    use HasFactory;
    protected $table = 'SEG_CambioEstadoNovedad';
    public $timestamps = false;

    protected $fillable = ['novedad', 'estado', 'fechaInicio', 'fechaCierre', 'observaciones'];
    protected $casts = [
        'fechaInicio' => 'date',
    ];

    public function estado()
    {
        return $this->belongsTo(SegEstadoReclamacion::class, 'estado_id', 'id');
    }
}
