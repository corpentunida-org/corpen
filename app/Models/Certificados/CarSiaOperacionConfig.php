<?php

namespace App\Models\Certificados;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CarSiaOperacionConfig extends Model
{
    use HasFactory, SoftDeletes;

    // 1. Especificar la tabla exacta
    protected $table = 'car_sia_operaciones_config';

    // 2. Campos asignables masivamente
    protected $fillable = [
        'numero_bloque',
        'id_car_sia_operaciones',
        'id_car_sia_config',
        'estado_notificacion',
    ];

    // 3. Conversión de tipos de datos (Casting)
    protected $casts = [
        'estado_notificacion' => 'boolean',
    ];

    // 4. Relaciones

    // Puede pertenecer a una operación específica (es nullable)
    public function operacion()
    {
        return $this->belongsTo(CarSiaOperacion::class, 'id_car_sia_operaciones');
    }

    // Pertenece obligatoriamente a una configuración base
    public function configuracionBase()
    {
        return $this->belongsTo(CarSiaConfig::class, 'id_car_sia_config');
    }
}
