<?php

namespace App\Models\Indicators;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class IndRespuestas extends Model
{
    use HasFactory;

    protected $table = 'Ind_respuestas'; 

    protected $fillable = [
        'pregunta_id',
        'text',
    ];

}
