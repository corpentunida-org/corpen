<?php

namespace App\Models\Maestras;

use Illuminate\Database\Eloquent\Model;

class Pais extends Model
{
    protected $table = 'paises';

    protected $fillable = ['codigo_iso', 'nombre'];
}
