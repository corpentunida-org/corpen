<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserSesion extends Model
{
    protected $table = 'sesiones_usuario';

    protected $fillable = [
        'user_id',
        'login_at',
        'last_activity_at',
        'logout_at',
        'ip',
    ];

    protected $casts = [
        'login_at' => 'datetime',
        'last_activity_at' => 'datetime',
        'logout_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Duración en segundos: hasta el logout si cerró sesión explícitamente, o hasta la última
     * actividad conocida si no (cierre de pestaña, expiración) — esa es la mejor aproximación
     * posible sin un logout registrado.
     */
    public function getDuracionSegundosAttribute(): int
    {
        $fin = $this->logout_at ?? $this->last_activity_at;

        return max(0, $this->login_at->diffInSeconds($fin));
    }
}
