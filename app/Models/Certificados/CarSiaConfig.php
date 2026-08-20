<?php

namespace App\Models\Certificados;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CarSiaConfig extends Model
{
    use HasFactory, SoftDeletes;

    // 1. Especificar la tabla exacta
    protected $table = 'car_sia_config';

    // 2. Campos asignables masivamente
    protected $fillable = [
        'id_car_sia_acciones_vencimiento',
        'parametros',
        'frecuencia_recordatorio_dias',
    ];

    // 3. Conversión de tipos de datos (Casting)
    // Esto es crucial para que Laravel convierta el JSONB a un Array de PHP automáticamente
    protected $casts = [
        'parametros' => 'array',
    ];

    // 4. Relaciones
    // Una configuración pertenece a una acción de vencimiento
    public function accionVencimiento()
    {
        return $this->belongsTo(CarSiaAccionVencimiento::class, 'id_car_sia_acciones_vencimiento');
    }

    // Una configuración general puede aplicarse a múltiples configuraciones de operación (pivote)
    public function operacionesConfig()
    {
        return $this->hasMany(CarSiaOperacionConfig::class, 'id_car_sia_config');
    }
}
