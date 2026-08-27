<?php

namespace App\Models\Maestras;

use Illuminate\Database\Eloquent\Model;

class EstadoCivil extends Model
{
    protected $table = 'estados_civiles';

    protected $fillable = ['codigo', 'nombre'];
}
