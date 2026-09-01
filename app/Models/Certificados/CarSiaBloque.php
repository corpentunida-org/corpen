<?php

namespace App\Models\Certificados;

use Illuminate\Database\Eloquent\Model;

class CarSiaBloque extends Model
{
    protected $table = 'car_sia_bloques';

    // Declaración explícita de todos los campos que se pueden llenar masivamente
    protected $fillable = [
        'numero_bloque',
        'id_periodo',
        'descripcion',
        'estado',
    ];

    /**
     * Relación hacia arriba: Un Bloque pertenece a un Periodo (Mes/Año)
     */
    public function periodo()
    {
        return $this->belongsTo(CarSiaPeriodo::class, 'id_periodo');
    }

    /**
     * Relación hacia abajo: Un Bloque agrupa muchas Operaciones.
     * Enlazamos usando la columna de negocio 'numero_bloque' en lugar del 'id'.
     */
    public function operaciones()
    {
        return $this->hasMany(CarSiaOperacion::class, 'numero_bloque', 'numero_bloque');
    }
}
