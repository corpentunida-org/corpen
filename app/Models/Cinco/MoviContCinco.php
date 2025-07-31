<?php

namespace App\Models\Cinco;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Maestras\maeTerceros;

class MoviContCinco extends Model
{
    use HasFactory;
    protected $table = 'CIN_MoviCont';

    public function cuentaContable()
    {
        return $this->belongsTo(CuentasContables::class, 'cuenta', 'id');
    }

    public function tercero()
    {
        return $this->belongsTo(maeTerceros::class, 'Cedula', 'Cod_Ter');
    }

}
