<?php

namespace App\Models\Cinco;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CuentasContables extends Model
{
    use HasFactory;
    protected $table = 'CIN_Cuentas';
    protected $primaryKey = 'id';

    public function movContable()
    {
        return $this->hasMany(MoviContCinco::class, 'Cuenta','id');
    }

}