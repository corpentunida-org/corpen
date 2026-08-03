<?php

namespace App\Models\Rsv;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Representa un inmueble dentro del catálogo disponible para reservas.
 */
class CatalogoInmueble extends Model
{
    protected $table = 'rsv_catalogo_inmueble';

    protected $fillable = [
        'name',
        'city',
        'ubicacion',
        'active',
        'capacidad_maxima',
        'precio_base_noche',
        'tipo_inmueble_id',
    ];

    protected $casts = [
        'active' => 'boolean',
        'capacidad_maxima' => 'integer',
        'precio_base_noche' => 'decimal:2',
    ];

    public function tarifasTemporadas(): HasMany
    {
        return $this->hasMany(TarifaTemporada::class, 'id_rsv_catalogo_inmueble');
    }

    public function bloqueosCalendario(): HasMany
    {
        return $this->hasMany(BloqueoCalendario::class, 'id_rsv_catalogo_inmueble');
    }

    public function multimedia(): HasMany
    {
        return $this->hasMany(InmuebleMultimedia::class, 'id_rsv_catalogo_inmueble');
    }

    public function reservas(): HasMany
    {
        return $this->hasMany(Reserva::class, 'id_rsv_catalogo_inmueble');
    }
}
