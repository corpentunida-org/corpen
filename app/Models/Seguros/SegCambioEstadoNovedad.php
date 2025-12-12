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
        'fechaCierre' => 'datetime',
        'fechaInicio' => 'datetime',
    ];

    public function estadosname()
    {
        return $this->belongsTo(SegEstadosNovedad::class, 'estado', 'id');
    }
}
