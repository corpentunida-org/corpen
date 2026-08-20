<?php

namespace App\Models\Certificados;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Certificados\CarSiaTipoEvento;
use Illuminate\Database\Eloquent\SoftDeletes;

class CarSiaTipo extends Model
{
    use HasFactory, SoftDeletes;

    // 1. Especificar la tabla exacta
    protected $table = 'car_sia_tipos';

    // 2. Campos asignables masivamente
    protected $fillable = [
        'nombre',
        'estado',
        'estructura_radicado',
    ];

    // 3. Conversión de tipos de datos (Casting)
    protected $casts = [
        'estado' => 'boolean',
    ];

    // 4. Relaciones (Opcional)
    // Un tipo de este catálogo define las tipologías o subcategorías de los eventos de las operaciones
    public function tiposEvento()
    {
        return $this->hasMany(CarSiaTipoEvento::class, 'id_car_sia_tipos');
    }
}
