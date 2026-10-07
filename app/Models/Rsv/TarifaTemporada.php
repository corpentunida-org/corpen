<?php

namespace App\Models\Rsv;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TarifaTemporada extends Model
{
    protected $table = 'rsv_tarifas_temporadas';

    protected $fillable = [
        'id_rsv_catalogo_inmueble',
        'nombre_temporada',
        'fecha_inicio',
        'fecha_fin',
        'precio_noche',
        'precio_fin_semana',
        'precio_minimo_reserva',
        'dias_maximos', 
        'active',
    ];

    protected $casts = [
        'fecha_inicio' => 'date',
        'fecha_fin' => 'date',
        'precio_noche' => 'decimal:2',
        'precio_fin_semana' => 'decimal:2',
        'precio_minimo_reserva' => 'decimal:2',
        'dias_maximos' => 'integer',
        'active' => 'boolean',
    ];

    public function inmueble(): BelongsTo
    {
        return $this->belongsTo(CatalogoInmueble::class, 'id_rsv_catalogo_inmueble');
    }
}
