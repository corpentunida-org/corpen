<?php

namespace App\Models\Soportes;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class ScpEstado extends Model
{
    use HasFactory;

    protected $table = 'scp_estados'; // Nombre de la tabla en la BD

    protected $fillable = [
        'nombre',
        'descripcion',
        'color',
    ];

    /**
     * Id de un estado por su nombre (ej. 'Revision', 'Cerrado'), en caché. Varios puntos del
     * módulo tenían el id hardcodeado (1/2/3/4) — hoy coincide con el catálogo real, pero se
     * rompería en silencio si alguien reordena o recrea esas filas. Buscar por nombre es estable
     * ante eso.
     */
    public static function idPorNombre(string $nombre): ?int
    {
        return Cache::remember('scp_estado_id_'.strtolower($nombre), 3600, function () use ($nombre) {
            return static::whereRaw('LOWER(nombre) = ?', [strtolower($nombre)])->value('id');
        });
    }

    public static function olvidarCache(): void
    {
        foreach (static::pluck('nombre') as $nombre) {
            Cache::forget('scp_estado_id_'.strtolower($nombre));
        }
    }
}
