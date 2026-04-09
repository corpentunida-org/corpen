<?php

namespace App\Models\Maestras;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Maestras\MaeCongregacion;

class MaeDistritos extends Model
{
    use HasFactory;

    protected $table = 'MaeDistritos';

    // 👇 AGREGAR ESTO URGENTE 👇
    protected $primaryKey = 'COD_DIST';
    public $incrementing = false;
    protected $keyType = 'string';
    // 👆 ======================= 👆

    protected $fillable = ['COD_DIST', 'NOM_DIST', 'DETALLE', 'COMPUEST'];

    public function congregaciones()
    {
        return $this->hasMany(MaeCongregacion::class, 'distrito', 'COD_DIST');
    }

    public function terceros()
    {
        return $this->hasMany(MaeTerceros::class, 'cod_dist', 'COD_DIST');
    }
}
