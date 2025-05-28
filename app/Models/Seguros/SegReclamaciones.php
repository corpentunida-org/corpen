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
        'telcontacto',
        'estado',
        'poliza_id',
        'cedulaContacto',
        'idBeneficiario',
        'valor_asegurado',
        'porreclamar',
        'fecha_desembolso',
        'finReclamacion',
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

    public function diagnostico()
    {
        return $this->belongsTo(SegDiagnosticos::class, 'idDiagnostico', 'id');
    }

    public function estadoReclamacion()
    {
        return $this->belongsTo(SegEstadoReclamacion::class, 'estado', 'id');
    }
}