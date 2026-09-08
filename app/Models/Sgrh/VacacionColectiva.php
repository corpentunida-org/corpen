<?php

namespace App\Models\Sgrh;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;

class VacacionColectiva extends Model
{
    protected $table = 'sgrh_vacacion_colectivas';

    protected $fillable = [
        'tipo',
        'fecha_inicio',
        'fecha_fin',
        'descripcion',
        'alcance',
        'estado',
        'user_id',
    ];

    protected $casts = [
        'fecha_inicio' => 'date',
        'fecha_fin' => 'date',
    ];

    public function usuario()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function alcances()
    {
        return $this->hasMany(VacacionColectivaAlcance::class, 'vacacion_colectiva_id');
    }

    public function solicitudes()
    {
        return $this->hasMany(VacacionSolicitud::class, 'vacacion_colectiva_id');
    }

    /**
     * IDs de sgrh_areas incluidas (solo tiene sentido cuando alcance='area').
     */
    public function areaIds(): array
    {
        return $this->alcances()->where('tipo', 'area')->pluck('referencia_id')->all();
    }

    /**
     * IDs de sgrh_empleados incluidos directamente (solo cuando alcance='empleados').
     */
    public function empleadoIds(): array
    {
        return $this->alcances()->where('tipo', 'empleado')->pluck('referencia_id')->all();
    }

    /**
     * Si este decreto aplica al empleado dado, según su alcance — usado tanto para generar las
     * solicitudes automáticas (obligatoria) como para validar un bloqueo contra una solicitud
     * individual (VacacionValidador).
     */
    public function aplicaA(Empleado $empleado): bool
    {
        return match ($this->alcance) {
            'empresa' => true,
            'area' => in_array($empleado->cargo?->sgrh_area_id, $this->areaIds(), true),
            'empleados' => in_array($empleado->id, $this->empleadoIds(), true),
            default => false,
        };
    }
}
