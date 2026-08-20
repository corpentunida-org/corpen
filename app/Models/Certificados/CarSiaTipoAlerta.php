<?php

namespace App\Models\Certificados;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CarSiaTipoAlerta extends Model
{
    use HasFactory, SoftDeletes;

    // 1. Especificar la tabla exacta
    protected $table = 'car_sia_tipos_alerta';

    // 2. Campos asignables masivamente
    protected $fillable = [
        'nombre',
    ];

    // 3. Relaciones (Opcional)
    // Un tipo de alerta puede estar configurado en muchas alertas operativas pivote
    public function operacionesAlertas()
    {
        return $this->hasMany(CarSiaOperacionAlerta::class, 'id_car_sia_tipos_alerta');
    }
}
