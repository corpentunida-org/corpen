<?php

namespace App\Models\Soportes;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class ScpTipoObservacion extends Model
{
    use HasFactory;

    protected $table = 'scp_tipo_observacions'; // Nombre de la tabla en la BD

    protected $fillable = [
        'nombre',
    ];

    /** Mismo criterio que ScpEstado::idPorNombre() — ver comentario allá. */
    public static function idPorNombre(string $nombre): ?int
    {
        return Cache::remember('scp_tipo_observacion_id_'.strtolower($nombre), 3600, function () use ($nombre) {
            return static::whereRaw('LOWER(nombre) = ?', [strtolower($nombre)])->value('id');
        });
    }
}
