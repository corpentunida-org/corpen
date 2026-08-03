<?php

namespace App\Models\Rsv;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class OrigenReserva extends Model
{
    protected $table = 'rsv_origen_reservas';

    protected $fillable = [
        'nombre',
    ];

    public function reservas(): HasMany
    {
        return $this->hasMany(Reserva::class, 'id_rsv_origen_reservas');
    }
}
