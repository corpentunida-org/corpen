<?php

namespace App\Models\Rsv;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ReservaHuesped extends Model
{
    protected $table = 'rsv_reserva_huespedes';

    protected $fillable = [
        'id_rsv_reservas',
        'id_user_registrador',
        'nombre',
        'apellidos',
        'tipo_documento',
        'numero_documento',
        'es_titular',
    ];

    protected $casts = [
        'es_titular' => 'boolean',
    ];

    public function reserva(): BelongsTo
    {
        return $this->belongsTo(Reserva::class, 'id_rsv_reservas');
    }

    public function registrador(): BelongsTo
    {
        return $this->belongsTo(\App\Models\User::class, 'id_user_registrador');
    }
}
