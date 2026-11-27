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
        'max_acompanantes',             // sin contar al titular; null = capacidad del inmueble - 1
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
        'max_acompanantes' => 'integer',
        'active' => 'boolean',
    ];

    public function inmueble(): BelongsTo
    {
        return $this->belongsTo(CatalogoInmueble::class, 'id_rsv_catalogo_inmueble');
    }

    /**
     * Tarifa que rige una reserva: se busca por INMUEBLE y AÑO DE LA RESERVA.
     * ÚNICA fuente de esta regla (la usan ReservaController al validar/guardar y el
     * Portal Cliente al mostrar precios), para que lo que ve el cliente sea lo que se valida.
     *
     * Una propiedad puede tener varias tarifas activas (año en curso + años futuros).
     *   1) Activa que cubre TODA la estadía [check-in, check-out].
     *   2) Activa que cubre el CHECK-IN (la del año de la reserva si cruza de año).
     *   3) Año sin tarifa (p. ej. un año futuro aún no registrado): la ÚLTIMA registrada,
     *      entendida como la activa de temporada más reciente.
     * Nunca devuelve tarifas inactivas. Sin ninguna activa → null.
     */
    public static function aplicable(int $idInmueble, \DateTimeInterface $checkIn, ?\DateTimeInterface $checkOut = null): ?self
    {
        $fi = \Illuminate\Support\Carbon::parse($checkIn)->toDateString();
        $ff = $checkOut ? \Illuminate\Support\Carbon::parse($checkOut)->toDateString() : $fi;

        $base = fn () => static::where('id_rsv_catalogo_inmueble', $idInmueble)->where('active', true);

        return $base()->whereDate('fecha_inicio', '<=', $fi)->whereDate('fecha_fin', '>=', $ff)
                ->orderBy('fecha_inicio', 'desc')->first()
            ?? $base()->whereDate('fecha_inicio', '<=', $fi)->whereDate('fecha_fin', '>=', $fi)
                ->orderBy('fecha_inicio', 'desc')->first()
            ?? $base()->orderBy('fecha_inicio', 'desc')->orderBy('id', 'desc')->first();
    }
}
