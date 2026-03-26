<?php

namespace App\Models\Interacciones;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class IntNextAction extends Model
{
    use HasFactory;

    protected $table = 'int_next_actions';

    protected $fillable = ['id','name'];

    public function seguimientos()
    {
        // Un tipo de acción (ej. "Llamar") aparece en muchos seguimientos
        return $this->hasMany(IntSeguimiento::class, 'next_action_type');
    }
}
