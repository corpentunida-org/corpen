<?php

namespace App\Models\Sgrh;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;

class ContratoModificacion extends Model
{
    protected $table = 'sgrh_contrato_modificaciones';

    protected $fillable = [
        'contrato_id',
        'causal',
        'observacion',
        'user_id',
    ];

    public function contrato()
    {
        return $this->belongsTo(Contrato::class, 'contrato_id');
    }

    public function usuario()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
