<?php

namespace App\Models\Demografia;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Direccion extends Model
{
    use HasFactory;

    // Nombre de la tabla en la base de datos
    protected $table = 'geo_direcciones';

    // Definir la llave primaria
    protected $primaryKey = 'id_direccion';

    // Campos que se pueden llenar masivamente
    protected $fillable = [
        'id_direccion',
        'calle',
        'numero',
        'codigo_postal',
        'id_ciudad',
    ];

    /**
     * Relación: Una dirección pertenece a una ciudad.
     */
    public function ciudad()
    {
        return $this->belongsTo(Ciudad::class, 'id_ciudad', 'id_ciudad');
    }
}