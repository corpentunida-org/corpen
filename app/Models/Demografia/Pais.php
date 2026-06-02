<?php

namespace App\Models\Demografia;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Demografia\Region;

class Pais extends Model
{
    use HasFactory;

    // Nombre de la tabla en la base de datos
    protected $table = 'geo_paises';

    // Definir la llave primaria
    protected $primaryKey = 'codigo_iso';

    // Indicar que la llave primaria no es autoincremental
    public $incrementing = false;

    // Indicar el tipo de dato de la llave primaria
    protected $keyType = 'string';

    // Campos que se pueden llenar masivamente
    protected $fillable = [
        'codigo_iso',
        'nombre',
    ];

    /**
     * Relación: Un país tiene muchas regiones.
     */
    public function regiones()
    {
        return $this->hasMany(Region::class, 'iso_pais', 'codigo_iso');
    }
}