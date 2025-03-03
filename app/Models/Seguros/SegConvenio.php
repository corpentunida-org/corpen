<?php

namespace App\Models\Seguros;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SegConvenio extends Model
{
    use HasFactory;
    protected $table = 'seg_convenios';
    protected $fillable = [
        'id',
        'idConvenio',
        'nombre',
        'idAseguradora',
        'seg_proveedor_id',
        'fecha_inicio',
        'fecha_fin',
    ];
    public function plan()
    {
        return $this->hasMany(SegPlan::class, 'seg_convenio_id', 'idConvenio');
    }
}