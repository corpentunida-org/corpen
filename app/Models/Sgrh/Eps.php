<?php

namespace App\Models\Sgrh;

use Illuminate\Database\Eloquent\Model;

class Eps extends Model
{
    protected $table = 'sgrh_eps';

    protected $fillable = ['nombre', 'activo'];
}
