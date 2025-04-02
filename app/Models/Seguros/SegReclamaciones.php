<?php

namespace App\Models\Seguros;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SegReclamaciones extends Model
{
    use HasFactory;
    protected $table = 'SEG_reclamaciones';
    protected $fillable = [
        'cedulaAsegurado',
        'idCobertura',
        'idDiagnostico',
        'otro',
        'fechaSiniestro',
        'fechaContacto',
        'horaContacto',
        'nombreContacto',
        'parentescoContacto',
        'estado',
        'poliza_id',
        'nombre_contingente',
        'idBeneficiario'
    ];

    public function asegurado()
    {
        return $this->belongsTo(SegAsegurado::class, 'cedulaAsegurado', 'cedula');
    }

    public function tercero()
    {
        return $this->belongsTo(SegTercero::class, 'cedulaAsegurado', 'cedula');
    }

    public function cobertura()
    {
        return $this->belongsTo(SegCobertura::class, 'idCobertura', 'id');
    }

    public function estadoReclamacion()
    {
        return $this->belongsTo(SegEstadoReclamacion::class, 'estado', 'id');
    }
}