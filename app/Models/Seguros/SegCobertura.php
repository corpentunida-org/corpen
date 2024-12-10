<?php

namespace App\Models\Seguros;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SegCobertura extends Model
{
    use HasFactory;
    protected $table = 'seg_coberturas';

    public function planes()
    {
        return $this->belongsToMany(
            SegPlan::class,
            'seg_plan_cobertura',
            'cobertura_id',
            'plan_id'
        )->withPivot('valorAsegurado', 'Prima');
    }
}
