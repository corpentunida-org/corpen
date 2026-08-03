<?php

namespace App\Models\Rsv;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Casts\AsArrayObject;

class ItinerarioEvento extends Model
{
    protected $table = 'rsv_itinerarios_eventos';

    protected $fillable = [
        'id_rsv_reservas',
        'titulo_evento',
        'hora_inicio',
        'hora_fin',
        'distribucion_detalle',
    ];

    protected $casts = [
        'hora_inicio' => 'datetime',
        'hora_fin' => 'datetime',
        'distribucion_detalle' => AsArrayObject::class,
    ];

    public function reserva(): BelongsTo
    {
        return $this->belongsTo(Reserva::class, 'id_rsv_reservas');
    }
}
