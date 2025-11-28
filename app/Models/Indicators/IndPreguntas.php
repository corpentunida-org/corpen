<?php

namespace App\Models\Indicators;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class IndPreguntas extends Model
{
    use HasFactory;

    protected $table = 'Ind_preguntas'; 

    protected $fillable = [
        'text',
    ];

    public function respuestas() {
        return $this->hasMany(IndRespuesta::class);
    }

}
