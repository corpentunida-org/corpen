<?php

namespace App\Models\Seguros;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SegTipoNovedad extends Model
{
    use HasFactory;
    protected $table = 'Seg_TipoNovedad';
    protected $fillable = [
        'id',
        'nombre',  
    ];
}
