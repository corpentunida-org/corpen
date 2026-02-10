<?php

namespace App\Models\Correspondencia;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\User;

class Proceso extends Model
{
    use HasFactory;

    protected $table = 'corr_procesos';

    protected $fillable = [
        'flujo_id',
        'detalle',
        'usuario_creador_id',
    ];

    /**
     * Relación: pertenece a un flujo de trabajo
     */
    public function flujo()
    {
        return $this->belongsTo(FlujoDeTrabajo::class, 'flujo_id');
    }

    /**
     * Relación: usuario creador
     */
    public function creador()
    {
        return $this->belongsTo(User::class, 'usuario_creador_id');
    }
    public function usuarios()
    {
        return $this->belongsToMany(\App\Models\User::class, 'corr_procesos_users', 'proceso_id', 'user_id')
                    ->withPivot('detalle')
                    ->withTimestamps();
    }
}
