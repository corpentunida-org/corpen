<?php

namespace App\Models\Interacciones;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class IntNextAction extends Model
{
    use HasFactory;

    protected $table = 'int_next_actions';

    protected $fillable = ['name'];

    public function interactions()
    {
        return $this->hasMany(Interaction::class, 'next_action_type');
    }
}
