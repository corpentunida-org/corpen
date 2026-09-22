<?php

namespace App\Models\Exequiales;

use App\Models\Demografia\Ciudad;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Exequiales\ExServicioComentarios;

class ExMonitoria extends Model
{
    use HasFactory;
    protected $table = 'EXE_MAEC_EXSER';
    //protected $table = 'pruebaServicios';
    protected $fillable = ['fechaRegistro', 'horaFallecimiento', 'cedulaTitular', 'nombreTitular', 'cedulaFallecido', 'nombreFallecido', 'fechaFallecimiento', 'lugarFallecimiento', 'tipoMuerte', 'ciudad_fallecimiento_id', 'parentesco', 'traslado', 'contacto', 'telefonoContacto', 'Contacto2', 'telefonoContacto2', 'municipio'];

    const TIPOS_MUERTE = ['Natural', 'Accidental', 'Suicidio', 'Homicidio', 'Indeterminada'];

    public function asociado()
    {
        return $this->belongsTo(ComaeExCli::class, 'cedulaTitular', 'cod_cli');
    }
    public function beneficiario()
    {
        return $this->belongsTo(ComaeExRelPar::class, 'cedulaFallecido', 'cedula');
    }
    public function parentescoo()
    {
        return $this->belongsTo(Parentescos::class, 'parentesco', 'code');
    }

    public function ciudadFallecimiento()
    {
        return $this->belongsTo(Ciudad::class, 'ciudad_fallecimiento_id', 'id_ciudad');
    }

    public function comments()
    {
        return $this->hasMany(ExServicioComentarios::class, 'id_exser', 'id');
    }
}
