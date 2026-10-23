<?php

namespace App\Models\Rsv;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Status extends Model
{
    use SoftDeletes; // Aplicando eliminación lógica

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