<?php

namespace App\Models\Rsv;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class HistorialEstado extends Model
{
    protected $table = 'rsv_historial_estados';

    protected $fillable = [
        'id_rsv_reservas',
        'id_rsv_statuses_anterior',
        'id_rsv_statuses_nuevo',
        'id_user',
        'comentario',
    ];

    public function reserva(): BelongsTo
    {
        return $this->belongsTo(Reserva::class, 'id_rsv_reservas');
    }

    public function estadoAnterior(): BelongsTo
    {
        return $this->belongsTo(Status::class, 'id_rsv_statuses_anterior');
    }

    public function estadoNuevo(): BelongsTo
    {
        return $this->belongsTo(Status::class, 'id_rsv_statuses_nuevo');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(\App\Models\User::class, 'id_user');
    }
}
