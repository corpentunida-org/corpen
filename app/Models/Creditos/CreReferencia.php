<?php

namespace App\Models\Creditos;

use App\Models\Maestras\MaeTerceros;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CreReferencia extends Model
{
    protected $table = 'cre_referencias';

    protected $fillable = [
        'cod_ter',
        'tipo', // familiar | comercial
        'nombre',
        'cedula',
        'telefono',
        'relacion',
        'observacion',
    ];

    public function tercero(): BelongsTo
    {
        return $this->belongsTo(MaeTerceros::class, 'cod_ter', 'cod_ter');
    }
}
