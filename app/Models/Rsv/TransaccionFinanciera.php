<?php

namespace App\Models\Rsv;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

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
        'soporte_pago',
    ];

    protected $casts = [
        'monto' => 'decimal:2',
    ];

    /**
     * Genera la URL de acceso al soporte de pago.
     *
     * - Si no existe soporte, devuelve '#'.
     * - Si ya es una URL completa, la devuelve tal cual.
     * - Si es una ruta de S3, genera una URL temporal de 30 minutos.
     * - Si ocurre un error, devuelve '#'.
     */
    protected function urlSoportePago(): Attribute
    {
        return Attribute::make(
            get: function () {
                $value = $this->soporte_pago;

                if (!$value) {
                    return '#';
                }

                // Si ya contiene una URL HTTP/HTTPS válida, se conserva.
                if (
                    filter_var($value, FILTER_VALIDATE_URL)
                    && in_array(
                        strtolower(parse_url($value, PHP_URL_SCHEME) ?? ''),
                        ['http', 'https'],
                        true
                    )
                ) {
                    return $value;
                }

                try {
                    return Storage::disk('s3')->temporaryUrl(
                        $value,
                        now()->addMinutes(30)
                    );
                } catch (\Throwable $e) {
                    report($e);

                    return '#';
                }
            }
        );
    }

    public function reserva(): BelongsTo
    {
        return $this->belongsTo(
            Reserva::class,
            'id_rsv_reservas'
        );
    }

    public function pasarela(): BelongsTo
    {
        return $this->belongsTo(
            Pasarela::class,
            'id_rsv_pasarela'
        );
    }
}
