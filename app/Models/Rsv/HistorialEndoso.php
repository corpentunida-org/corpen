<?php

namespace App\Models\Rsv;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class HistorialEndoso extends Model
{
    protected $table = 'rsv_historial_endosos';

    protected $fillable = [
        'id_rsv_reservas',
        'id_user_anterior',
        'id_user_nuevo',
        'id_user_autorizado_por',
        'motivo_endoso',
    ];

    public function reserva(): BelongsTo
    {
        return $this->belongsTo(Reserva::class, 'id_rsv_reservas');
    }

    public function usuarioAnterior(): BelongsTo
    {
        return $this->belongsTo(\App\Models\User::class, 'id_user_anterior');
    }

    public function usuarioNuevo(): BelongsTo
    {
        return $this->belongsTo(\App\Models\User::class, 'id_user_nuevo');
    }

    public function autorizadoPor(): BelongsTo
    {
        return $this->belongsTo(\App\Models\User::class, 'id_user_autorizado_por');
    }
}
