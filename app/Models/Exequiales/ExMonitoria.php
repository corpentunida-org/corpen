<?php

namespace App\Models\Exequiales;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Exequiales\ExServicioComentarios;

class ExMonitoria extends Model
{
    use HasFactory;
    protected $table = 'EXE_MAEC_EXSER';
    //protected $table = 'pruebaServicios';
    protected $fillable = ['fechaRegistro', 'horaFallecimiento', 'cedulaTitular', 'nombreTitular', 'cedulaFallecido', 'nombreFallecido', 'fechaFallecimiento', 'lugarFallecimiento', 'parentesco', 'traslado', 'contacto', 'telefonoContacto', 'Contacto2', 'telefonoContacto2', 'municipio'];
    public function asociado()
    {
        return $this->belongsTo(ComaeExCli::class, 'cedulaTitular', 'cedula');
    }
    public function beneficiario()
    {
        return $this->belongsTo(ComaeExRelPar::class, 'cedulaFallecido', 'cedula');
    }
    public function parentescoo()
    {
        return $this->belongsTo(Parentescos::class, 'parentesco', 'codPar');
    }

    public function comments()
    {
        return $this->hasMany(ExServicioComentarios::class, 'id_exser', 'id');
    }
}
