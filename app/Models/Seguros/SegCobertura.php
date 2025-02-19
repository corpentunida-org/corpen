<?php

namespace App\Models\Seguros;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SegCobertura extends Model
{
    use HasFactory;
    protected $table = 'seg_coberturas';
    protected $fillable = [
        'id',
        'nombre',
        'prima',
        'convenio_id',
    ];

    public function planes()
    {
        return $this->belongsToMany(
            SegPlan::class,
            'seg_plan_cobertura',
            'cobertura_id',
            'plan_id'
        )->withPivot('valorAsegurado', 'valorCobertura');
    }

    public function reclamacion()
    {
        return $this->hasMany(SegReclamaciones::class, 'idCobertura', 'id');
    }
}
