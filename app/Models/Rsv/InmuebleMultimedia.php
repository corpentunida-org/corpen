<?php

namespace App\Models\Rsv;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class InmuebleMultimedia extends Model
{
    protected $table = 'rsv_inmueble_multimedia';

    protected $fillable = [
        'id_rsv_catalogo_inmueble',
        'url_archivo',
        'tipo_multimedia',
        'orden',
        'es_portada',
    ];

    protected $casts = [
        'es_portada' => 'boolean',
        'orden' => 'integer',
    ];

    public function inmueble(): BelongsTo
    {
        return $this->belongsTo(CatalogoInmueble::class, 'id_rsv_catalogo_inmueble');
    }
}
