<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ExMonitoria extends Model
{
    use HasFactory;
    protected $table = 'MAEC_EXSER';
    protected $fillable = [
        'fechaRegistro',
        'horaFallecimiento',
        'cedulaTitular',
        'cedulaFallecido',
        'fechaFallecimiento',
        'lugarFallecimiento',
        'parentesco',
        'traslado',
        'contacto',
        'telefonoContacto',
        'Contacto2',
        'telefonoContacto2',
        'factura',
        'valor',
    ];
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
}