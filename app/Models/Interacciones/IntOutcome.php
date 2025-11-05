<?php

namespace App\Models\Interacciones;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class IntOutcome extends Model
{
    use HasFactory;

    protected $table = 'int_outcomes';

    protected $fillable = ['id','name'];

    public function interactions()
    {
        return $this->hasMany(Interaction::class, 'outcome');
    }
}
