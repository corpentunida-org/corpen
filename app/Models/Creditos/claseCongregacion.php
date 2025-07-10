<?php

namespace App\Models\Creditos;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


use App\Models\Creditos\Congregacion;

class claseCongregacion extends Model
{
use HasFactory;

    protected $table = 'claseCongregacion';

    protected $fillable = [
        'id',
        'nombre',

    ];
    //crear una funcion que relacionara la otra tabla - congregaciones es el nombre de la funcion ya que mas adelante la voy a llamar
    //relacionar el nombre con la misma tabla para que no hayan confuciones 
    public function congregaciones()
{
    return $this->hasMany(Congregacion::class, 'clase', 'id');
}

}
