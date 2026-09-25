<?php

namespace App\Models\Interacciones;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

/**
 * Agentes exentos de las alertas forzadas — compartida entre Interacciones y Soportes, separada
 * por tipo ('correo'/'pantalla'/'informe'). Ver la migración crear_alertas_omitidos (y
 * agregar_tipo_informe_a_alertas_omitidos) para el detalle de qué cubre cada tipo.
 */
class IntAlertaOmitido extends Model
{
    protected $table = 'int_alerta_omitidos';

    protected $fillable = ['user_id', 'tipo', 'created_by'];

    public function usuario()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function creadoPor()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /** IDs de usuario omitidos de un tipo — cacheado 5 min (se consulta en cada envío/carga). */
    public static function idsOmitidos(string $tipo): array
    {
        return Cache::remember("int_alerta_omitidos_{$tipo}", 300, function () use ($tipo) {
            return self::where('tipo', $tipo)->pluck('user_id')->toArray();
        });
    }

    public static function estaOmitido(int $userId, string $tipo): bool
    {
        return in_array($userId, self::idsOmitidos($tipo), true);
    }

    public static function olvidarCache(): void
    {
        Cache::forget('int_alerta_omitidos_correo');
        Cache::forget('int_alerta_omitidos_pantalla');
        Cache::forget('int_alerta_omitidos_informe');
    }
}
