<?php

namespace App\Models\Seguros;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

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
        'valor_asegurado',
        'valorpagaraseguradora',
        'created_at',
        'updated_at',
    ];

    public function asegurado()
    {
        return $this->belongsTo(SegAsegurado::class, 'seg_asegurado_id','cedula');
    }

    public function plan()
    {
        return $this->belongsTo(SegPlan::class, 'seg_plan_id', 'id');
    }

    public function tercero()
    {
        return $this->belongsTo(SegTercero::class, 'seg_asegurado_id', 'cedula');
    }

    public function esreclamacion(){
        return $this->belongsTo(SegReclamaciones::class, 'reclamacion', 'id');
    }   
}