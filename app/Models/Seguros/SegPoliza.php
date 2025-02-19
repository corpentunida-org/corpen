<?php

namespace App\Models\Seguros;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SegPoliza extends Model
{
    use HasFactory;
    protected $table = 'SEG_polizas';

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