<?php

namespace App\Models\Rsv;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Review extends Model
{
    protected $table = 'rsv_reviews';

    protected $fillable = [
        'id_rsv_reservas',
        'id_user_autor',
        'id_rsv_tipo_receptor',
        'tipo_evaluacion',
        'puntuacion',
        'comentario',
    ];

    protected $casts = [
        'puntuacion' => 'integer',
    ];

    public function reserva(): BelongsTo
    {
        return $this->belongsTo(Reserva::class, 'id_rsv_reservas');
    }

    public function autor(): BelongsTo
    {
        return $this->belongsTo(\App\Models\User::class, 'id_user_autor');
    }

    public function tipoReceptor(): BelongsTo
    {
        return $this->belongsTo(TipoReceptor::class, 'id_rsv_tipo_receptor');
    }
}
