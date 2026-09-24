<?php

namespace App\Models\Soportes;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;

class ScpAlertaDecision extends Model
{
    protected $table = 'scp_alerta_decisiones';

    protected $fillable = ['user_id', 'fecha', 'decision'];

    protected $casts = [
        'fecha' => 'date',
    ];

    public function usuario()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
