<?php

namespace App\Models\Rsv;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Mensaje extends Model
{
    protected $table = 'rsv_mensajes';

    protected $fillable = [
        'id_rsv_reservas',
        'id_user_remitente',
        'tipo_mensaje',
        'contenido',
        'url_archivo',
        'leido_en',
    ];

    protected $casts = [
        'leido_en' => 'datetime',
    ];

    public function reserva(): BelongsTo
    {
        return $this->belongsTo(Reserva::class, 'id_rsv_reservas');
    }

    public function remitente(): BelongsTo
    {
        return $this->belongsTo(\App\Models\User::class, 'id_user_remitente');
    }
}
