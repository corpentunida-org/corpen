<?php

namespace App\Models\Indicators;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class IndQuiz extends Model
{
    use HasFactory;

    protected $table = 'Ind_quiz';

    protected $fillable = ['nombre', 'total_preguntas', 'estado', 'usuario_creador', 'area'];
    public function preguntas()
    {
        return $this->hasMany(IndPreguntas::class, 'ref_quiz');
    }
}
