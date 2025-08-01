<?php

namespace App\Models\Seguros;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SegPlan extends Model
{
    use HasFactory;
    protected $table = 'SEG_plans';
    protected $primaryKey = 'id';
    public $incrementing = true;
    protected $keyType = 'int';
    protected $fillable = ['name', 'valor', 'prima', 'seg_convenio_id', 'condicion_id'];

    public function condicion()
    {
        return $this->belongsTo(SegCondicion::class, 'condicion_id', 'id');
    }

    public function coberturas()
    {
        return $this->belongsToMany(SegCobertura::class, 'seg_plan_cobertura', 'plan_id', 'cobertura_id')->withPivot('valorAsegurado', 'valorCobertura', 'porcentaje', 'extra');
    }

    public function polizas()
    {
        return $this->hasMany(SegPlan::class, 'seg_asegurado_id');
    }

    public function convenio()
    {
        return $this->belongsTo(SegConvenio::class, 'seg_convenio_id', 'idConvenio');
    }
}
