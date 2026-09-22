<?php

namespace App\Models\Maestras;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;

class CongregacionImportacion extends Model
{
    protected $table = 'congregacion_importaciones';

    protected $fillable = ['user_id', 'archivo_nombre', 'resumen'];

    protected $casts = [
        'resumen' => 'array',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
