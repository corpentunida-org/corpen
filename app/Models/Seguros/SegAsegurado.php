<?php

namespace App\Models\Seguros;

use App\Models\Maestras\maeTerceros;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SegAsegurado extends Model
{
    use HasFactory;
    protected $table = 'SEG_asegurados';
    protected $fillable = [
        'cedula',         
        'parentesco',      
        'titular',
        'valorpAseguradora'
    ];

    /* public function terceroAF()
    {
        return $this->belongsTo(SegTercero::class, 'titular', 'cedula');
    } */

    public function terceroAF()
    {
        return $this->belongsTo(maeTerceros::class, 'titular', 'cod_ter');
    }

    /* public function tercero()
    {
        return $this->belongsTo(SegTercero::class, 'cedula', 'cedula');
    } */
    public function tercero()
    {
        return $this->belongsTo(maeTerceros::class, 'cedula', 'cod_ter');
    }

    public function polizas()
    {
        return $this->hasMany(SegPoliza::class, 'seg_asegurado_id', 'cedula');
    }

    public function reclamacion()
    {
        return $this->hasMany(SegReclamaciones::class, 'cedulaAsegurado', 'cedula');
    }

    public function novedades()
    {
        return $this->hasMany(SegNovedades::class, 'id_asegurado', 'cedula');
    }
}