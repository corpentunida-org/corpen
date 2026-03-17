<?php

namespace App\Models\Interacciones;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class IntOutcome extends Model
{
    use HasFactory;

    protected $table = 'int_outcomes';

    protected $fillable = ['id','name','estado'];

    public function interactions()
    {
        return $this->hasMany(Interaction::class, 'outcome');
    }
    public function seguimientos()
    {
        // Un resultado (ej. "No contesta") aparece en muchos seguimientos
        return $this->hasMany(IntSeguimiento::class, 'outcome');
    }
}
