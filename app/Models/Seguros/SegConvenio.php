<?php

namespace App\Models\Seguros;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SegConvenio extends Model
{
    use HasFactory;
    protected $table = 'seg_convenios';
    public function plan()
    {
        return $this->hasMany(SegPlan::class, 'seg_convenio_id', 'id');
    }
}
