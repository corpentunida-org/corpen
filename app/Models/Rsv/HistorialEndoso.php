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

    /* Español: los que usa la VISTA (reservas.blade.php) */
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

    /* ═══ ALIAS (inglés): los nombres EXACTOS que exige el CONTROLADOR
       en load(['reserva', 'userAnterior', 'userNuevo', 'autorizadoPor']).
       Sin estos, el store responde 500 aunque el endoso ya se haya guardado. ═══ */
    public function userAnterior(): BelongsTo
    {
        return $this->usuarioAnterior();
    }

    public function userNuevo(): BelongsTo
    {
        return $this->usuarioNuevo();
    }
}
