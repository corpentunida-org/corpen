<?php

namespace App\Models\Seguros;

use App\Models\Maestras\maeTerceros;
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
        return $this->belongsTo(maeTerceros::class, 'cedulaAsegurado', 'cod_ter');
    }
    public function terceroAlt()
    {
        return $this->belongsTo(SegTercero::class, 'cedula', 'cedula');
    }
    public function getNombreTerceroAttribute()
    {
        if ($this->tercero) {
            return $this->tercero->nom_ter;
        }

        if ($this->terceroAlt) {
            return $this->terceroAlt->nombre;
        }

        return ' ';
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

    public function cambiosEstado(){
        return $this->hasMany(SegCambioEstadoReclamacion::class, 'reclamacion_id', 'id');
    } 
}