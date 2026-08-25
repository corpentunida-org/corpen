<?php

namespace App\Models\Certificados;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

// Importa el modelo con el que te vas a relacionar (ajusta el namespace si está en otra ruta)
use App\Models\Certificados\CarSiaOperacionLinea;

class CarSiaLinea extends Model
{
    use HasFactory;

    protected $table = 'car_sia_lineas';

    protected $fillable = [
        'cuenta',
        'nombre',
    ];

    /**
     * Relación: Una línea puede tener muchas operaciones-líneas asociadas.
     */
    public function operacionesLineas()
    {
        // Se definen explícitamente los parámetros: 
        // 1. Modelo relacionado
        // 2. Llave foránea en la tabla car_sia_operaciones_lineas ('id_car_sia_lineas')
        // 3. Llave local en esta tabla car_sia_lineas ('cuenta')
        return $this->hasMany(CarSiaOperacionLinea::class, 'id_car_sia_lineas', 'cuenta');
    }
}