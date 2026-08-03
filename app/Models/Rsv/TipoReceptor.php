<?php

namespace App\Models\Rsv;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TipoReceptor extends Model
{
    protected $table = 'rsv_tipo_receptor';

    protected $fillable = [
        'codigo',
        'nombre',
    ];

    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class, 'id_rsv_tipo_receptor');
    }
}
