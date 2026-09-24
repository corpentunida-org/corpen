<?php

namespace App\Models\Interacciones;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;

class IntAlertaDecision extends Model
{
    protected $table = 'int_alerta_decisiones';

    protected $fillable = ['user_id', 'fecha', 'decision'];

    protected $casts = [
        'fecha' => 'date',
    ];

    public function usuario()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
