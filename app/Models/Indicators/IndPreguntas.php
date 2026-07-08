<?php

namespace App\Models\Indicators;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class IndPreguntas extends Model
{
    use HasFactory;

    protected $table = 'Ind_preguntas';

    protected $fillable = ['text', 'ref_quiz'];
    
    public function quiz()
    {
        return $this->belongsTo(IndQuiz::class, 'ref_quiz');
    }

    public function respuestas()
    {
        return $this->hasMany(IndRespuesta::class);
    }
}
