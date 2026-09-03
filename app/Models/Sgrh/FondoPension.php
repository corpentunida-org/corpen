<?php

namespace App\Models\Sgrh;

use Illuminate\Database\Eloquent\Model;

class FondoPension extends Model
{
    protected $table = 'sgrh_fondos_pension';

    protected $fillable = ['nombre', 'activo'];
}
