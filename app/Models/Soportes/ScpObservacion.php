<?php

namespace App\Models\Soportes;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;

class ScpObservacion extends Model
{
    use HasFactory;

    protected $table = 'scp_observaciones';

    protected $fillable = [
        'observacion',
        'timestam',
        'id_scp_soporte',
        'id_scp_estados',
        'id_users', //USUARIO QUE CREA LA OBSERVACIO
        'id_users_asignado', //USUARIO A QUIEN ES ESCALADO
        'id_tipo_observacion',
    ];

    /*
    |--------------------------------------------------------------------------
    | RELACIONES
    |--------------------------------------------------------------------------
    */

    // Soporte al que pertenece la observación
    public function soporte()
    {
        return $this->belongsTo(ScpSoporte::class, 'id_scp_soporte');
    }

    // Estado relacionado
    public function estado()
    {
        return $this->belongsTo(ScpEstado::class, 'id_scp_estados');
    }

    // Tipo de observación
    public function tipoObservacion()
    {
        return $this->belongsTo(ScpTipoObservacion::class, 'id_tipo_observacion');
    }

    // Usuario que crea la observación
    public function usuario()
    {
        return $this->belongsTo(User::class, 'id_users');
    }

    // Usuario asignado (si aplica)
    public function usuarioAsignado()
    {
        return $this->belongsTo(User::class, 'id_users_asignado');
    }
}
