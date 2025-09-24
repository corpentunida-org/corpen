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
        'id_users',
        'id_users_asignado',
        'id_tipo_observacion',
    ];

    /*
    |--------------------------------------------------------------------------
    | RELACIONES
    |--------------------------------------------------------------------------
    */

    // Relación con Soporte
    public function soporte()
    {
        return $this->belongsTo(ScpSoporte::class, 'id_scp_soporte');
    }

    // Relación con Estado
    public function estado()
    {
        return $this->belongsTo(ScpEstado::class, 'id_scp_estados');
    }

    // Relación con Usuario
    public function usuario()
    {
        return $this->belongsTo(User::class, 'id_users');
    }

    // Relación con Tipo de Observación
    public function tipoObservacion()
    {
        return $this->belongsTo(ScpTipoObservacion::class, 'id_tipo_observacion');
    }

    public function usuarioAsignado()
    {
        return $this->belongsTo(User::class, 'id_users_asignado');
    }

    public function scpUsuarioAsignado() {
        return $this->belongsTo(ScpUsuario::class, 'id_users_asignado');
    }


}
