<?php

namespace App\Models\Sgrh;

use Illuminate\Database\Eloquent\Model;

class Estudio extends Model
{
    protected $table = 'sgrh_estudios';

    protected $fillable = [
        'empleado_id',
        'programa',
        'institucion_educativa',
        'tipo_formacion',
        'nivel_formacion',
        'graduado',
        'fecha_terminacion',
    ];

    protected $casts = [
        'graduado' => 'boolean',
        'fecha_terminacion' => 'date',
    ];

    public function empleado()
    {
        return $this->belongsTo(Empleado::class, 'empleado_id');
    }
}
