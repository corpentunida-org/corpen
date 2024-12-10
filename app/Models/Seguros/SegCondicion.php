<?php

namespace App\Models\Seguros;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SegCondicion extends Model
{
    use HasFactory;
    protected $table = 'seg_condiciones';
    public function plan()
    {
        return $this->hasMany(SegPlan::class, 'condicion_id', 'id');
    }
}
