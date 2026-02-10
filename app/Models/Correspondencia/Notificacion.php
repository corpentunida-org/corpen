<?php

namespace App\Models\Correspondencia;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\User;

class Notificacion extends Model
{
    use HasFactory;

    protected $table = 'corr_notificaciones';

    protected $fillable = [
        'proceso_origen_id',
        'proceso_destino_id',
        'mensaje',
        'estado',
        'usuario_destino_id',
        'usuario_envia_id',
        'fecha_leida',
    ];

    protected $casts = [
        'fecha_leida' => 'datetime',
    ];

    /**
     * Proceso origen
     */
    public function procesoOrigen()
    {
        return $this->belongsTo(Proceso::class, 'proceso_origen_id');
    }

    /**
     * Proceso destino
     */
    public function procesoDestino()
    {
        return $this->belongsTo(Proceso::class, 'proceso_destino_id');
    }

    /**
     * Usuario que recibe
     */
    public function usuarioDestino()
    {
        return $this->belongsTo(User::class, 'usuario_destino_id');
    }

    /**
     * Usuario que envía
     */
    public function usuarioEnvia()
    {
        return $this->belongsTo(User::class, 'usuario_envia_id');
    }
}
