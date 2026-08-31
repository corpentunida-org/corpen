<?php

namespace App\Models\Sgrh;

use Illuminate\Database\Eloquent\Model;

class Arl extends Model
{
    protected $table = 'sgrh_arl';

    protected $fillable = ['nombre', 'activo'];
}
