<?php

namespace App\Models\Interacciones;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;

class IntAlertaEscalacion extends Model
{
    protected $table = 'int_alerta_escalaciones';

    protected $fillable = ['user_id', 'fecha', 'dias_consecutivos'];

    protected $casts = [
        'fecha' => 'date',
    ];

    public function usuario()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
