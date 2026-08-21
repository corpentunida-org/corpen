<?php

namespace App\Models\Certificados;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Creditos\LineaCredito;

/**
 * Detalle (Líneas) de la Operación.
 */
class CarSiaOperacionLinea extends Model
{
    use HasFactory, SoftDeletes;

    // 1. Especificar la tabla exacta en la base de datos
    protected $table = 'car_sia_operaciones_lineas';

    // 2. Campos asignables masivamente
    // - numero_bloque: Lo hereda de "car_sia_operaciones".
    // - observacion: Configurado Segun Regla.
    // - calificacion: Configurado Segun Regla.
    // - fecha_venci: Fecha de vencimiento de la línea de operación.
    // - fecha_ultimo_recordatorio: Configurado Segun Regla.
    // - dias_mora_automaticos: Configurado Segun Regla.
    // - procesado_en: Fecha y hora en que se procesó el registro.
    protected $fillable = [
        'id_car_sia_operaciones',
        'id_cre_lineas_creditos',
        'numero_bloque',
        'observacion',
        'calificacion',
        'fecha_venci',
        'id_car_sia_estados',
        'fecha_ultimo_recordatorio',
        'dias_mora_automaticos',
        'procesado_en',
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
     * Identificador de la línea de crédito asociada.
     */
    public function lineaCredito()
    {
        return $this->belongsTo(LineaCredito::class, 'id_cre_lineas_creditos');
    }

    /**
     * Relación con los Logs de la Operación (Auditoría).
     * Una línea de operación puede tener múltiples registros en su historial de eventos/cambios.
     */
    public function logs()
    {
        return $this->hasMany(CarSiaOperacionLog::class, 'id_car_sia_operaciones_lineas');
    }
}
