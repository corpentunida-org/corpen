<?php

namespace App\Models\Demografia;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Demografia\Direccion;
use App\Models\Demografia\Subregion;

class Ciudad extends Model
{
    use HasFactory;

    // Nombre de la tabla en la base de datos
    protected $table = 'geo_ciudades';

    // Definir la llave primaria
    protected $primaryKey = 'id_ciudad';

    // Campos que se pueden llenar masivamente
    protected $fillable = [
        'id_ciudad',
        'nombre',
        'id_subregion',
    ];

    /**
     * Relación: Una ciudad pertenece a una subregión.
     */
    public function subregion()
    {
        return $this->belongsTo(Subregion::class, 'id_subregion', 'id_subregion');
    }

    /**
     * Relación: Una ciudad tiene muchas direcciones asociadas.
     */
    public function direcciones()
    {
        return $this->hasMany(Direccion::class, 'id_ciudad', 'id_ciudad');
    }
}