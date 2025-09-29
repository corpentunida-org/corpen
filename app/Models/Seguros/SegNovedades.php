<?php

namespace App\Models\Seguros;

use App\Models\Maestras\maeTerceros;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Seguros\SegAsegurado;
use App\Models\Seguros\SegCambioEstadoNovedad;
use App\Models\Seguros\SegEstadosNovedad;
use App\Models\Seguros\SegPlan;
use App\Models\Seguros\SegPoliza;
use App\Models\Seguros\SegTercero;
use App\Models\Seguros\SegBeneficiario;

class SegNovedades extends Model
{
    use HasFactory;
    /* protected $table = 'Seg_novedadaes';
    protected $fillable = [
        'id_poliza',
        'id_asegurado',
        'retiro',
        'plan',
        'valorpagar',
        'valorAsegurado',
        'fechaNovedad',
        'observaciones',
        'valorPrimaPlan'
    ]; */

    protected $table = 'Seg_novedadaes02';
    protected $fillable = [
        'id_poliza',
        'id_asegurado',
        'tipo',
        'estado',
        'id_plan',
        'valorAsegurado',
        'primaAseguradora',
        'primaCorpen',
        'extraprima',
        'formulario'
    ];

    /* public function tercero()
    {
        return $this->belongsTo(SegTercero::class, 'id_asegurado', 'cedula');
    } */
    public function tercero()
    {
        return $this->belongsTo(maeTerceros::class, 'id_asegurado', 'cod_ter');
    }
    public function terceroAlt()
    {
        return $this->belongsTo(SegTercero::class, 'id_asegurado', 'cedula');
    }
    public function getNombreTerceroAttribute()
    {
        if ($this->tercero) {
            return $this->tercero->nom_ter;
        }

        else if ($this->terceroAlt) {
            return $this->terceroAlt->nombre;
        }
        return '';
    }

    public function asegurado()
    {
        return $this->belongsTo(SegAsegurado::class, 'id_asegurado', 'cedula');
    }
    public function plan()
    {
        return $this->belongsTo(SegPlan::class, 'planNuevo', 'id');
    }
    public function poliza()
    {
        return $this->belongsTo(SegPoliza::class, 'id_poliza', 'id');
    }
    public function estadoNovedad()
    {
        return $this->belongsTo(SegEstadosNovedad::class, 'estado', 'id');
    }
    public function cambiosEstado(){
        return $this->hasMany(SegCambioEstadoNovedad::class, 'novedad', 'id');
    } 

    public function beneficiario(){
        return $this->belongsTo(SegBeneficiario::class, 'beneficiario_id', 'id');
    }

}
