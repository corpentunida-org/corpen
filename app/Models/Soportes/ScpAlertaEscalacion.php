<?php

namespace App\Models\Soportes;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;

class ScpAlertaEscalacion extends Model
{
    protected $table = 'scp_alerta_escalaciones';

    protected $fillable = ['user_id', 'fecha', 'dias_consecutivos'];

    protected $casts = [
        'fecha' => 'date',
    ];

    public function usuario()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
