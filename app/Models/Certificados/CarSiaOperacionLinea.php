<?php

namespace App\Models\Certificados;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Creditos\LineaCredito;

class CarSiaOperacionLinea extends Model
{
    use HasFactory, SoftDeletes;

    // 1. Especificar la tabla exacta
    protected $table = 'car_sia_operaciones_lineas';

    // 2. Campos asignables masivamente
    protected $fillable = [
        'id_car_sia_operaciones',
        'id_cre_lineas_creditos',
        'numero_bloque',
        'observacion',
        'calificacion',
        'fecha_venci',
        'id_car_sia_estados_operacion',
        'fecha_ultimo_recordatorio',
        'dias_mora_automaticos',
        'procesado_en',
    ];

    // 3. Conversión de tipos de datos (Casting)
    protected $casts = [
        'fecha_venci' => 'datetime',
        'fecha_ultimo_recordatorio' => 'datetime',
        'procesado_en' => 'datetime',
        'dias_mora_automaticos' => 'integer',
    ];

    // 4. Relaciones

    // Pertenece a una operación padre
    public function operacion()
    {
        return $this->belongsTo(CarSiaOperacion::class, 'id_car_sia_operaciones');
    }

    // Pertenece a un estado de operación (Fase 3)
    public function estadoOperacion()
    {
        return $this->belongsTo(CarSiaEstadoOperacion::class, 'id_car_sia_estados_operacion');
    }

    public function lineaCredito()
    {
        return $this->belongsTo(LineaCredito::class, 'id_cre_lineas_creditos');
    }

    // Una línea de operación puede tener múltiples registros en el historial (Logs)
    public function logs()
    {
        return $this->hasMany(CarSiaOperacionLog::class, 'id_car_sia_operaciones_lineas');
    }
}
