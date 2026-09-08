<?php

namespace App\Models\Sgrh;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;

class VacacionSolicitud extends Model
{
    protected $table = 'sgrh_vacacion_solicitudes';

    protected $fillable = [
        'empleado_id',
        'fecha_inicio',
        'fecha_fin',
        'dias_habiles',
        'tipo',
        'estado',
        'es_adelantada',
        'autorizada_por_rrhh_id',
        'aprobador_user_id',
        'rol_aprobador',
        'fecha_resolucion',
        'motivo_rechazo',
        'vacacion_colectiva_id',
        'observaciones',
    ];

    protected $casts = [
        'fecha_inicio' => 'date',
        'fecha_fin' => 'date',
        'dias_habiles' => 'decimal:2',
        'es_adelantada' => 'boolean',
        'fecha_resolucion' => 'datetime',
    ];

    public function empleado()
    {
        return $this->belongsTo(Empleado::class, 'empleado_id');
    }

    public function autorizadaPorRrhh()
    {
        return $this->belongsTo(User::class, 'autorizada_por_rrhh_id');
    }

    public function aprobador()
    {
        return $this->belongsTo(User::class, 'aprobador_user_id');
    }

    public function vacacionColectiva()
    {
        return $this->belongsTo(VacacionColectiva::class, 'vacacion_colectiva_id');
    }

    public function scopePendientes($query)
    {
        return $query->where('estado', 'pendiente');
    }

    public function scopeAprobadas($query)
    {
        return $query->where('estado', 'aprobada');
    }

    /**
     * Solapa con el rango [$inicio, $fin] — mismo criterio de intersección de intervalos usado
     * para bloquear solicitudes propias duplicadas y para chequear decretos de bloqueo.
     */
    public function scopeSolapaCon($query, $inicio, $fin)
    {
        return $query->where('fecha_inicio', '<=', $fin)->where('fecha_fin', '>=', $inicio);
    }
}
