<?php

namespace App\Models\Maestras;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Maestras\MaeCongregacion; 

class MaeMunicipios extends Model
{
    use HasFactory;

    protected $table = 'MaeMunicipios';

    // Si la tabla de municipios no tiene created_at/updated_at, descomenta la siguiente línea:
    // public $timestamps = false;

    protected $fillable = [
        'codigo_Dane',
        'nombre',
        'id_departamento',
    ];

    /**
     * Relación uno a muchos con Congregaciones
     */
    public function congregaciones()
    {
        // 'municipio' es la llave foránea en la tabla congregaciones
        // 'id' es la llave primaria en MaeMunicipios
        return $this->hasMany(MaeCongregacion::class, 'municipio', 'id');
    }

    /**
     * Relación inversa: Un municipio puede tener muchos activos
     */
    public function activos()
    {
        return $this->hasMany(\App\Models\Inventario\InvActivo::class, 'id_MaeMunicipios', 'id');
    }
}