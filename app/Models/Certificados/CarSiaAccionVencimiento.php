<?php

namespace App\Models\Certificados;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CarSiaAccionVencimiento extends Model
{
    use HasFactory, SoftDeletes;

    // 1. Especificar la tabla exacta
    protected $table = 'car_sia_acciones_vencimiento';

    // 2. Campos asignables masivamente
    protected $fillable = [
        'nombre',
        'estado',
    ];

    // 3. Conversión de tipos de datos (Casting)
    protected $casts = [
        'estado' => 'boolean',
    ];

    // 4. Relaciones (Opcional)
    // Una acción de vencimiento es utilizada en la tabla de configuración core
    public function configuraciones()
    {
        return $this->hasMany(CarSiaConfig::class, 'id_car_sia_acciones_vencimiento');
    }
}
