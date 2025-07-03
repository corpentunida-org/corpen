<?php

namespace App\Models\Seguros;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SegPlan_cobertura extends Model
{
    use HasFactory;
    protected $table = 'seg_plan_cobertura';
    protected $fillable = [
        'plan_id',
        'cobertura_id',
        'valorAsegurado',
        'valorCobertura',
        'porcentaje',
        'extra',
    ];

    public function cobertura()
    {
        return $this->belongsTo(SegCobertura::class, 'cobertura_id', 'id');
    }
}
