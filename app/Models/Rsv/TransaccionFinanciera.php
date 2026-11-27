<?php

namespace App\Models\Rsv;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage;

class TransaccionFinanciera extends Model
{
    use SoftDeletes;

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
    /**
     * Disco donde se guardan los soportes: S3 cuando hay bucket configurado (producción);
     * si no (entorno de desarrollo sin AWS), el disco "public" local. Antes se forzaba S3 y,
     * sin bucket, la subida fallaba en silencio y quedaba soporte_pago = "0".
     */
    public static function discoSoportes(): string
    {
        return config('filesystems.disks.s3.bucket') ? 's3' : 'public';
    }

    /** ¿Tiene un soporte de pago real? (vacío o "0" = subida fallida → no cuenta). */
    public function tieneSoporte(): bool
    {
        $v = trim((string) ($this->soporte_pago ?? ''));
        return $v !== '' && $v !== '0';
    }

    /** Contabilidad lo rechazó / anuló (o falló): no cuenta como pago reportado. */
    public function estaDescartada(): bool
    {
        $e = mb_strtolower((string) ($this->estado_pago ?? ''));
        foreach (['rechaz', 'anul', 'cancel', 'fallid'] as $k) {
            if (str_contains($e, $k)) return true;
        }
        return false;
    }

    /** Soporte que cuenta para la reserva: archivo real y no rechazado / anulado. */
    public function esSoporteValido(): bool
    {
        return $this->tieneSoporte() && !$this->estaDescartada();
    }

    protected function urlSoportePago(): Attribute
    {
        return Attribute::make(
            get: function () {
                $value = $this->soporte_pago;

                if (!$value || trim((string) $value) === '0') {
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
                    if (self::discoSoportes() === 'public') {
                        return Storage::disk('public')->url($value);
                    }
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
