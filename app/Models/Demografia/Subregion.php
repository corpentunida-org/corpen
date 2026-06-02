<?php

namespace App\Models\Demografia;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Demografia\Ciudad;

class Subregion extends Model
{
    use HasFactory;

    // Nombre de la tabla en la base de datos
    protected $table = 'geo_subregiones';

    // Definir la llave primaria
    protected $primaryKey = 'id_subregion';

    // Campos que se pueden llenar masivamente
    protected $fillable = [
        'id_subregion',
        'nombre',
        'codigo',
        'id_region',
    ];

    /**
     * Relación: Una subregión pertenece a una región.
     */
    public function region()
    {
        return $this->belongsTo(Region::class, 'id_region', 'id_region');
    }

    /**
     * Relación: Una subregión tiene muchas ciudades.
     */
    public function ciudades()
    {
        return $this->hasMany(Ciudad::class, 'id_subregion', 'id_subregion');
    }
}