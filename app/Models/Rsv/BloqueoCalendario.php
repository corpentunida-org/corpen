<?php

namespace App\Models\Rsv;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes; // <-- Importar

class BloqueoCalendario extends Model
{
    //use SoftDeletes; // <-- pendiente no hay columnas de borrado suave en la tabla, pero se agrega por si acaso

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
    public $timestamps = false;
    
    public function inmueble(): BelongsTo
    {
        return $this->belongsTo(CatalogoInmueble::class, 'id_rsv_catalogo_inmueble');
    }
}
