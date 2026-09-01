<?php

namespace App\Models\Certificados;

use Illuminate\Database\Eloquent\Model;

class CarSiaPeriodo extends Model
{
    protected $table = 'car_sia_periodos';

    // Declaración explícita de todos los campos que se pueden llenar masivamente
    protected $fillable = [
        'anio',
        'mes',
        'nombre',
        'abierto',
    ];

    // Forzar el tipo de dato al consultar
    protected $casts = [
        'abierto' => 'boolean',
        'anio'    => 'integer',
        'mes'     => 'integer',
    ];

    /**
     * Relación: Un Periodo (Mes/Año) agrupa muchos Bloques (Lotes)
     */
    public function bloques()
    {
        return $this->hasMany(CarSiaBloque::class, 'id_periodo');
    }
}
