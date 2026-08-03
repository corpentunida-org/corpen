<?php

namespace App\Models\Rsv;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BloqueoCalendario extends Model
{
    protected $table = 'rsv_bloqueos_calendario';

    protected $fillable = [
        'id_rsv_catalogo_inmueble',
        'fecha_inicio',
        'fecha_fin',
        'motivo',
    ];

    protected $casts = [
        'fecha_inicio' => 'date',
        'fecha_fin' => 'date',
    ];

    public function inmueble(): BelongsTo
    {
        return $this->belongsTo(CatalogoInmueble::class, 'id_rsv_catalogo_inmueble');
    }
}
