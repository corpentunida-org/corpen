<?php

namespace App\Models\Demografia;

use App\Models\Demografia\Pais;
use App\Models\Demografia\Subregion;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Region extends Model
{
    use HasFactory;

    // Nombre de la tabla en la base de datos
    protected $table = 'geo_regiones';

    // Definir la llave primaria
    protected $primaryKey = 'id_region';

    // Campos que se pueden llenar masivamente
    protected $fillable = [
        'id_region',
        'nombre',
        'codigo_iso',
        'iso_pais',
    ];

    /**
     * Relación: Una región pertenece a un país.
     */
    public function pais()
    {
        return $this->belongsTo(Pais::class, 'iso_pais', 'codigo_iso');
    }

    /**
     * Relación: Una región tiene muchas subregiones.
     */
    public function subregiones()
    {
        return $this->hasMany(Subregion::class, 'id_region', 'id_region');
    }
}