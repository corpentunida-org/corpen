<?php

namespace App\Models\Maestras;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


use App\Models\Maestras\Congregacion;

class maeMunicipios extends Model
{
use HasFactory;

    protected $table = 'MaeMunicipios';

    protected $fillable = [
        'id',
        'codigo_Dane',
        'nombre',
        'id_departamento',
    ];
    
    //crear una funcion que relacionara la otra tabla - congregaciones es el nombre de la funcion ya que mas adelante la voy a llamar
    //relacionar el nombre con la misma tabla para que no hayan confuciones 
        /**
     * Relación uno a muchos con Congregaciones
     * Un distrito puede tener muchas congregaciones
     */
    public function congregaciones()
    {
        return $this->hasMany(Congregacion::class, 'id', 'municipio');
    }

}
