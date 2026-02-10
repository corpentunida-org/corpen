<?php

namespace App\Models\Correspondencia;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\User;

class CorrespondenciaProceso extends Model
{
    use HasFactory;

    protected $table = 'corr_correspondencia_proceso';

    protected $fillable = [
        'id_correspondencia',
        'observacion',
        'estado',
        'id_proceso',
        'notificado_email',
        'fecha_gestion',
        'documento_arc',
        'fk_usuario',
    ];

    protected $casts = [
        'notificado_email' => 'boolean',
        'fecha_gestion' => 'datetime',
    ];

    /**
     * Correspondencia relacionada
     */
    public function correspondencia()
    {
        return $this->belongsTo(Correspondencia::class, 'id_correspondencia', 'id_radicado');
    }

    /**
     * Proceso asociado
     */
    public function proceso()
    {
        return $this->belongsTo(Proceso::class, 'id_proceso');
    }

    /**
     * Usuario responsable
     */
    public function usuario()
    {
        return $this->belongsTo(User::class, 'fk_usuario');
    }
}
