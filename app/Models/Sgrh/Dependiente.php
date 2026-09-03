<?php

namespace App\Models\Sgrh;

use Illuminate\Database\Eloquent\Model;

class Dependiente extends Model
{
    protected $table = 'sgrh_dependientes';

    protected $fillable = [
        'empleado_id',
        'nombre1',
        'nombre2',
        'apellido1',
        'apellido2',
        'tipo_documento',
        'documento_identificacion',
        'fecha_nacimiento',
        'genero',
        'parentesco',
    ];

    protected $casts = [
        'fecha_nacimiento' => 'date',
    ];

    public function empleado()
    {
        return $this->belongsTo(Empleado::class, 'empleado_id');
    }

    public function getNombreCompletoAttribute()
    {
        // trim + colapsar espacios: nombre2/apellido2 suelen venir vacíos, y concatenarlos
        // tal cual deja dobles espacios entre palabras.
        return trim(preg_replace('/\s+/', ' ', "{$this->nombre1} {$this->nombre2} {$this->apellido1} {$this->apellido2}"));
    }
}
