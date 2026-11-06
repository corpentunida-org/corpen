<?php

namespace App\Models\Rsv;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes; // <-- 1. Importar el Trait

class TarifaTemporada extends Model
{
    use SoftDeletes; // <-- 2. Usar el Trait para habilitar deleted_at

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
        'max_reservas_anuales_cliente', // <-- 3. Agregar el nuevo campo
        'active',
    ];

    protected $casts = [
        'fecha_inicio' => 'date',
        'fecha_fin' => 'date',
        'precio_noche' => 'decimal:2',
        'precio_fin_semana' => 'decimal:2',
        'precio_minimo_reserva' => 'decimal:2',
        'dias_maximos' => 'integer',
        'max_reservas_anuales_cliente' => 'integer', // <-- 4. Castear como entero
        'active' => 'boolean',
    ];

    public function inmueble(): BelongsTo
    {
        return $this->belongsTo(CatalogoInmueble::class, 'id_rsv_catalogo_inmueble');
    }
}
