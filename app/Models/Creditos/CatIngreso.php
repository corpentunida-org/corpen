<?php

namespace App\Models\Creditos;

use Illuminate\Database\Eloquent\Model;

class CatIngreso extends Model
{
    protected $table = 'cre_cat_ingresos';

    public $timestamps = false;

    protected $fillable = ['nombre', 'orden'];
}
