<?php

namespace App\Models\Archivo;

use Illuminate\Database\Eloquent\Relations\Pivot;

class GdoFuncionCargo extends Pivot
{
    // Definimos el namespace correcto y la tabla
    protected $table = 'gdo_funcion_cargo';

    // Como tu tabla pivot tiene un campo 'id' autoincremental, 
    // activamos esta propiedad para que Eloquent lo trate correctamente.
    public $incrementing = true;

    protected $fillable = [
        'gdo_funcion_id',
        'gdo_cargo_id',
        'estado'
    ];
}