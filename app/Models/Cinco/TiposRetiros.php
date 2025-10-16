<?php

namespace App\Models\Cinco;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TiposRetiros extends Model
{
    use HasFactory;
    protected $table = 'RET_TiposRetiros';
    protected $fillable = [
        'nombre',
        'activo'
    ];
}