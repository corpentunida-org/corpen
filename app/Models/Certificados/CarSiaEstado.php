<?php

namespace App\Models\Certificados;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CarSiaEstado extends Model
{
    use HasFactory, SoftDeletes;

    // 1. Especificar la tabla exacta
    protected $table = 'car_sia_estados'; //

    // 2. Campos asignables masivamente
    protected $fillable = [
        'nombre', //
    ];

    // ---------------------------------------------------
    // 3. RELACIONES DEL SISTEMA
    // ---------------------------------------------------

    /**
     * Un estado puede estar asignado a MUCHAS Líneas (Facturas/Detalle)
     */
    public function lineas()
    {
        // La columna exacta en car_sia_operaciones_lineas es 'id_car_sia_estados'
        return $this->hasMany(CarSiaOperacionLinea::class, 'id_car_sia_estados');
    }

    /**
     * Un estado tiene muchos registros en la tabla pivote/historial (Auditoría/Trazabilidad)
     */
    public function historialEstados()
    {
        // La columna en car_sia_estados_operacion es 'id_car_sia_estados'
        return $this->hasMany(CarSiaEstadoOperacion::class, 'id_car_sia_estados');
    }
}