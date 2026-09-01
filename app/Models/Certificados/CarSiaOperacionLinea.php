<?php

namespace App\Models\Certificados;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\User; // Añadido para la relación con usuarios

/**
 * Detalle (Líneas) de la Operación.
 */
class CarSiaOperacionLinea extends Model
{
    use HasFactory, SoftDeletes;

    // 1. Especificar la tabla exacta en la base de datos
    protected $table = 'car_sia_operaciones_lineas';

    // 2. Campos asignables masivamente
    protected $fillable = [
        'id_car_sia_operaciones',
        'id_factura',
        'id_car_sia_lineas', //cuenta
        'numero_bloque',
        'observacion',
        'calificacion',
        'fecha_venci',
        'id_car_sia_estados',
        'fecha_ultimo_recordatorio',
        'dias_mora_automaticos',
        'procesado_en',
        // --- CAMPOS NUEVOS DE AUDITORÍA Y CERTIFICADOS ---
        'id_user',
        'id_car_sia_tipos',
        'hash_certificado',
    ];

    // 3. Conversión de tipos de datos (Casting)
    protected $casts = [
        'fecha_venci'               => 'datetime',
        'fecha_ultimo_recordatorio' => 'datetime',
        'procesado_en'              => 'datetime',
        'dias_mora_automaticos'     => 'integer',
    ];

    // ---------------------------------------------------
    // 4. RELACIONES DEL SISTEMA
    // ---------------------------------------------------

    /**
     * Factura de Staging (car_sia_api) asociada a esta línea específica.
     */
    public function factura()
    {
        // Nota: Si la llave primaria en car_sia_api no es 'id' sino 'id_factura',
        // debes declararlo así: return $this->belongsTo(CarSiaApi::class, 'id_factura', 'id_factura');
        return $this->belongsTo(CarSiaApi::class, 'id_factura');
    }

    /**
     * Relación con la tabla de Staging (car_sia_api) a través del campo 'cuenta'.
     * Útil para cruzar la data cruda con la línea asignada.
     */
    public function apiPorCuenta()
    {
        return $this->belongsTo(CarSiaApi::class, 'id_car_sia_lineas', 'cuenta');
    }

    /**
     * Permite múltiples líneas por cada operación.
     */
    public function operacion()
    {
        return $this->belongsTo(CarSiaOperacion::class, 'id_car_sia_operaciones');
    }

    /**
     * Identificador del estado actual de la operación.
     */
    public function estadoOperacion()
    {
        return $this->belongsTo(CarSiaEstado::class, 'id_car_sia_estados');
    }

    /**
     * Identificador de la línea SIA asociada.
     */
    public function lineaSia()
    {
        // Se actualiza para apuntar explícitamente a la columna 'cuenta' en el modelo CarSiaLinea
        return $this->belongsTo(CarSiaLinea::class, 'id_car_sia_lineas', 'cuenta');
    }

    /**
     * Relación con los Logs de la Operación (Auditoría).
     * Una línea de operación puede tener múltiples registros en su historial de eventos/cambios.
     */
    public function logs()
    {
        return $this->hasMany(CarSiaOperacionLog::class, 'id_car_sia_operaciones_lineas');
    }

    // ---------------------------------------------------
    // 5. NUEVAS RELACIONES DE AUDITORÍA
    // ---------------------------------------------------

    /**
     * Usuario que generó/auditó la línea del certificado.
     */
    public function usuario()
    {
        return $this->belongsTo(User::class, 'id_user');
    }

    /**
     * Tipo de operación/evento en el momento de la auditoría.
     */
    public function tipoAuditoria()
    {
        return $this->belongsTo(CarSiaTipoOperacion::class, 'id_car_sia_tipos');
    }
}
