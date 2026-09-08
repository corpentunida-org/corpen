<?php

namespace App\Models\Sgrh;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;

class FestivoAjuste extends Model
{
    protected $table = 'sgrh_festivos_ajustes';

    protected $fillable = [
        'fecha',
        'tipo',
        'descripcion',
        'user_id',
    ];

    protected $casts = [
        'fecha' => 'date',
    ];

    public function usuario()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
