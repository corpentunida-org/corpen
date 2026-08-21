<?php

namespace App\Models\Certificados;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
// Importamos el modelo User por defecto de Laravel (ajusta si tu modelo está en otra ruta)
use App\Models\User;

/**
 * Registro de Auditoría y Trazabilidad (Logs).
 */
class CarSiaOperacionLog extends Model
{
    use HasFactory, SoftDeletes;

    // 1. Especificar la tabla exacta
    protected $table = 'car_sia_operaciones_logs';

    // 2. Campos asignables masivamente
    // - numero_bloque: Lo hereda de "car_sia_operaciones".
    // - ip: Dirección IP desde donde se ejecutó la acción.
    // - detalles_ejecucion: Detalles adicionales o respuesta técnica de la ejecución en formato JSON.
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
    protected $casts = [
        'detalles_ejecucion' => 'array',
    ];

    // 4. Relaciones

    /**
     * Identificador de la operación de la línea principal asociada al log.
     */
    public function lineaOperacion()
    {
        return $this->belongsTo(CarSiaOperacionLinea::class, 'id_car_sia_operaciones_lineas');
    }

    /**
     * Identificador del origen del evento (ej. Web, Cron, API).
     */
    public function origenEvento()
    {
        return $this->belongsTo(CarSiaOrigenEvento::class, 'id_car_sia_origenes_evento');
    }

    /**
     * Identificador del tipo de evento de auditoría registrado.
     */
    public function eventoAuditoria()
    {
        return $this->belongsTo(CarSiaEventoAuditoria::class, 'id_car_sia_eventos_auditoria');
    }

    /**
     * Identificador del usuario que realizó la acción (si aplica).
     */
    public function usuario()
    {
        return $this->belongsTo(User::class, 'id_user');
    }
}
