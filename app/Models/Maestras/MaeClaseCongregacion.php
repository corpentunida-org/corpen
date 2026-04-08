<?php

namespace App\Models\Maestras;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


use App\Models\Maestras\MaeCongregacion;

class MaeClaseCongregacion extends Model
{
use HasFactory;

    protected $table = 'MaeClaseCongregacion';

    protected $fillable = [
        'id',
        'nombre',

    ];
    
    //crear una funcion que relacionara la otra tabla - congregaciones es el nombre de la funcion ya que mas adelante la voy a llamar
    //relacionar el nombre con la misma tabla para que no hayan confuciones 
    
    public function congregaciones()
    {
        return $this->hasMany(MaeCongregacion::class, 'id', 'clase');
    }

}
