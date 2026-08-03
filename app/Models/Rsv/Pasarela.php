<?php

namespace App\Models\Rsv;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Pasarela extends Model
{
    protected $table = 'rsv_pasarelas';

    protected $fillable = [
        'nombre',
        'icono',
        'color',
    ];

    public function transacciones(): HasMany
    {
        return $this->hasMany(TransaccionFinanciera::class, 'id_rsv_pasarela');
    }
}
