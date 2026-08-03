<?php

namespace App\Models\Rsv;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TransaccionFinanciera extends Model
{
    protected $table = 'rsv_transacciones_financieras';

    protected $fillable = [
        'id_rsv_reservas',
        'monto',
        'moneda',
        'estado_pago',
        'id_rsv_pasarela',
        'metodo_pago',
        'referencia_externa',
    ];

    protected $casts = [
        'monto' => 'decimal:2',
    ];

    public function reserva(): BelongsTo
    {
        return $this->belongsTo(Reserva::class, 'id_rsv_reservas');
    }

    public function pasarela(): BelongsTo
    {
        return $this->belongsTo(Pasarela::class, 'id_rsv_pasarela');
    }
}
