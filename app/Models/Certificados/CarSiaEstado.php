<?php

namespace App\Models\Certificados;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CarSiaEstado extends Model
{
    use HasFactory, SoftDeletes;

    // 1. Especificar la tabla exacta
    protected $table = 'car_sia_estados';

    // 2. Campos asignables masivamente
    protected $fillable = [
        'nombre',
    ];

    // 3. Relaciones (Opcional)
    // Un estado de este catálogo se usa para registrar el historial de estados de las operaciones
    public function estadosOperacion()
    {
        return $this->hasMany(CarSiaEstadoOperacion::class, 'id_car_sia_estados');
    }
}
