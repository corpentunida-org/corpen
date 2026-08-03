<?php

namespace App\Models\Rsv;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Status extends Model
{
    protected $table = 'rsv_statuses';

    protected $fillable = [
        'name',
        'color_hex',
    ];

    public function reservas(): HasMany
    {
        return $this->hasMany(Reserva::class, 'id_rsv_statuses');
    }
}
