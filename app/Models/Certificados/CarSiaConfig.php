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
        'estado_activo',
    ];

    // 3. Conversión de tipos de datos (Casting)
    protected $casts = [
        'parametros' => 'array',
        'estado_activo' => 'boolean', 
    ];

    // 4. Relaciones
    public function accionVencimiento()
    {
        return $this->belongsTo(CarSiaAccionVencimiento::class, 'id_car_sia_acciones_vencimiento');
    }

    public function operacionesConfig()
    {
        return $this->hasMany(CarSiaOperacionConfig::class, 'id_car_sia_config');
    }
}
