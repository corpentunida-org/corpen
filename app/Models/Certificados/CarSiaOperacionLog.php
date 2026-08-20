<?php

namespace App\Models\Certificados;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
// Importamos el modelo User por defecto de Laravel (ajusta si tu modelo está en otra ruta)
use App\Models\User;

class CarSiaOperacionLog extends Model
{
    use HasFactory, SoftDeletes;

    // 1. Especificar la tabla exacta
    protected $table = 'car_sia_operaciones_logs';

    // 2. Campos asignables masivamente
    protected $fillable = [
        'numero_bloque',
        'id_car_sia_operaciones_lineas',
        'id_car_sia_origenes_evento',
        'id_car_sia_eventos_auditoria',
        'id_user',
        'ip',
        'detalles_ejecucion',
    ];

    // 3. Conversión de tipos de datos (Casting)
    // Transforma el JSONB de PostgreSQL en un Array de PHP automáticamente
    protected $casts = [
        'detalles_ejecucion' => 'array',
    ];

    // 4. Relaciones

    // Puede pertenecer a una línea de operación específica (es nullable)
    public function lineaOperacion()
    {
        return $this->belongsTo(CarSiaOperacionLinea::class, 'id_car_sia_operaciones_lineas');
    }

    // Pertenece a un origen de evento (ej. Web, Cron, API)
    public function origenEvento()
    {
        return $this->belongsTo(CarSiaOrigenEvento::class, 'id_car_sia_origenes_evento');
    }

    // Pertenece a un evento de auditoría específico
    public function eventoAuditoria()
    {
        return $this->belongsTo(CarSiaEventoAuditoria::class, 'id_car_sia_eventos_auditoria');
    }

    // Puede estar asociado a un usuario del sistema que disparó la acción
    public function usuario()
    {
        return $this->belongsTo(User::class, 'id_user');
    }
}
