<?php

namespace App\Models\Creditos;

use Illuminate\Database\Eloquent\Model;

class CatEgreso extends Model
{
    protected $table = 'cre_cat_egresos';

    public $timestamps = false;

    protected $fillable = ['nombre', 'orden'];
}
