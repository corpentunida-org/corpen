<?php

namespace App\Models\Seguros;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SegPlan extends Model
{
    use HasFactory;
    protected $table = 'SEG_plans';

    public function condicion()
    {
        return $this->belongsTo(SegCondicion::class, 'condicion_id', 'id');
    }

    public function coberturas()
    {
        return $this->belongsToMany(
        SegCobertura::class,'seg_plan_cobertura','plan_id','cobertura_id')
                ->withPivot('valorAsegurado', 'Prima');
    }

    public function poliza()
    {
        return $this->hasOne(SegPlan::class, 'seg_asegurado_id');
    }

    
}
