<?php

namespace App\Models\Interacciones;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class IntChannel extends Model
{
    use HasFactory;

    protected $table = 'int_channels'; 

    protected $fillable = [
        'id',
        'name',
    ];

    public function interactions()
    {
        return $this->hasMany(Interaction::class, 'interaction_channel');
    }

}
