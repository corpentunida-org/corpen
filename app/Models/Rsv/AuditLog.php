<?php

namespace App\Models\Rsv;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Casts\AsArrayObject;

class AuditLog extends Model
{
    protected $table = 'rsv_audit_logs';

    protected $fillable = [
        'id_user',
        'tabla_afectada',
        'registro_id',
        'accion',
        'datos_anteriores',
        'datos_nuevos',
        'ip_address',
    ];

    protected $casts = [
        'datos_anteriores' => AsArrayObject::class,
        'datos_nuevos' => AsArrayObject::class,
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(\App\Models\User::class, 'id_user');
    }
}
