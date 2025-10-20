<?php

namespace App\Models\Interacciones;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class IntType extends Model
{
    use HasFactory;

    protected $table = 'int_types';

    protected $fillable = ['name'];

    public function interactions()
    {
        return $this->hasMany(Interaction::class, 'interaction_type');
    }
}
