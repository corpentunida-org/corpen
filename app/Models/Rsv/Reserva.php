<?php

namespace App\Models\Rsv;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Reserva extends Model
{
    use SoftDeletes;

    protected $table = 'rsv_reservas';

    protected $fillable = [
        'codigo_reserva',
        'id_rsv_catalogo_inmueble',
        'id_user',
        'id_rsv_statuses',
        'fecha_inicio',
        'fecha_fin',
        'monto_total',
        'id_rsv_origen_reservas',
        'comentario_reserva',
    ];

    protected $casts = [
        'fecha_inicio' => 'datetime',
        'fecha_fin' => 'datetime',
        'monto_total' => 'decimal:2',
    ];

    // Relaciones (BelongsTo)
    public function inmueble(): BelongsTo
    {
        return $this->belongsTo(CatalogoInmueble::class, 'id_rsv_catalogo_inmueble');
    }

    public function cliente(): BelongsTo
    {
        return $this->belongsTo(\App\Models\User::class, 'id_user');
    }

    public function status(): BelongsTo
    {
        return $this->belongsTo(Status::class, 'id_rsv_statuses');
    }

    public function origen(): BelongsTo
    {
        return $this->belongsTo(OrigenReserva::class, 'id_rsv_origen_reservas');
    }

    // Relaciones (HasMany)
    public function huespedes(): HasMany
    {
        return $this->hasMany(ReservaHuesped::class, 'id_rsv_reservas');
    }

    public function transacciones(): HasMany
    {
        return $this->hasMany(TransaccionFinanciera::class, 'id_rsv_reservas');
    }

    public function itinerarios(): HasMany
    {
        return $this->hasMany(ItinerarioEvento::class, 'id_rsv_reservas');
    }

    public function mensajes(): HasMany
    {
        return $this->hasMany(Mensaje::class, 'id_rsv_reservas');
    }

    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class, 'id_rsv_reservas');
    }

    public function historialEstados(): HasMany
    {
        return $this->hasMany(HistorialEstado::class, 'id_rsv_reservas');
    }

    public function historialEndosos(): HasMany
    {
        return $this->hasMany(HistorialEndoso::class, 'id_rsv_reservas');
    }
}
