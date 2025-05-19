<?php

namespace App\Models\Seguros;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;
class SegTercero extends Model
{
    use HasFactory;
    
    protected $table = 'SEG_terceros';
    protected $fillable = [
        'cedula',
        'nombre',
        'fechaNacimiento',
        'telefono',
        'genero',
        'distrito'   
    ];
    public function getEdadAttribute()
    {
        if ($this->fechaNacimiento) {
            return Carbon::parse($this->fechaNacimiento)->age;
        }
        return null;
    }
    public function asegurados()
    {
        return $this->hasMany(SegAsegurado::class, 'cedula', 'cedula');
    }
    public function polizas()
    {
        return $this->hasMany(SegPoliza::class, 'seg_asegurado_id');
    }
    public function novedades()
    {
        return $this->hasMany(SegNovedades::class, 'id_asegurado', 'cedula');
    }
    public function reclamacion()
    {
        return $this->hasMany(SegReclamaciones::class, 'cedulaAsegurado','cedula');
    }
}
