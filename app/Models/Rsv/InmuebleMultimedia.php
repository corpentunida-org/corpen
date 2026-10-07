<?php

namespace App\Models\Rsv;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

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

    /**
     * Accesor inteligente para url_archivo.
     * Convierte la ruta guardada en S3 en una URL temporal segura al consultarla,
     * o respeta enlaces externos completos si ya los hubiera.
     */
    protected function urlArchivo(): Attribute
    {
        return Attribute::make(
            get: function ($value) {
                if (!$value) {
                    return '#';
                }

                // Si por alguna razón el campo ya contiene una URL web completa (http/https), la devolvemos tal cual
                if (filter_var($value, FILTER_VALIDATE_URL)) {
                    return $value;
                }

                try {
                    // Genera una URL temporal en S3 con duración de 30 minutos sin bloquear el listado con Storage::exists()
                    return Storage::disk('s3')->temporaryUrl($value, now()->addMinutes(30));
                } catch (\Exception $e) {
                    // Fallback seguro en caso de que el archivo no exista o falle la conexión con AWS
                    return '#';
                }
            }
        );
    }

    public function inmueble(): BelongsTo
    {
        return $this->belongsTo(CatalogoInmueble::class, 'id_rsv_catalogo_inmueble');
    }
}
