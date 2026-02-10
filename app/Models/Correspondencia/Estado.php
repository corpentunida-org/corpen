<?php

namespace App\Models\Correspondencia;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Estado extends Model
{
    use HasFactory;

    protected $table = 'corr_estados';

    protected $fillable = [
        'nombre','descripcion',
    ];

public function correspondencias()
{
    return $this->hasMany(Correspondencia::class, 'estado_id');
}


}
