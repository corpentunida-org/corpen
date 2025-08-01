<?php

namespace App\Models\Seguros;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Maestras\maeTerceros;


class SegPoliza extends Model
{
    use HasFactory;
    protected $table = 'SEG_polizas';
    protected $fillable = [
        'seg_asegurado_id',
        'seg_convenio_id',
        'numero',
        'active',
        'fecha_inicio',
        'fecha_fin',
        'fecha_novedad',
        'extra_prima',
        'seg_plan_id',
        'valor_prima',
        'reclamacion',
        'descuento',
        'descuentopor',
        'valor_asegurado',
        'valorpagaraseguradora',
        'primapagar',
        'created_at',
        'updated_at',
    ];

    public function asegurado()
    {
        return $this->belongsTo(SegAsegurado::class, 'seg_asegurado_id', 'cedula');
    }

    public function plan()
    {
        return $this->belongsTo(SegPlan::class, 'seg_plan_id', 'id');
    }

    /* public function tercero()
    {
        return $this->belongsTo(SegTercero::class, 'seg_asegurado_id', 'cedula');
    } */
    public function tercero()
    {
        return $this->belongsTo(maeTerceros::class, 'seg_asegurado_id', 'cod_ter');
    }

    public function esreclamacion()
    {
        return $this->hasMany(SegReclamaciones::class, 'poliza_id', 'id');
    }



}