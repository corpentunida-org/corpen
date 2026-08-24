<?php

namespace App\Models\Certificados;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CarSiaEstadoOperacion extends Model
{
    use HasFactory, SoftDeletes;

    // 1. Especificar la tabla exacta
    protected $table = 'car_sia_estados_operacion'; //

    // 2. Campos asignables masivamente
    protected $fillable = [
        'id_car_sia_operaciones',  //null - En inyectarBloques queda blanco
        'id_car_sia_estados', //
        'numero_bloque', // En inyectarBloques 
    ];

    // 3. Relaciones

    // Puede pertenecer a una operación específica (es nullable)
    public function operacion()
    {
        return $this->belongsTo(CarSiaOperacion::class, 'id_car_sia_operaciones');
    }

    // Pertenece obligatoriamente a un estado del catálogo
    public function estado()
    {
        return $this->belongsTo(CarSiaEstado::class, 'id_car_sia_estados');
    }
}
