<?php

namespace App\Models\Correspondencia;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\User;

class ProcesoUsuario extends Model
{
    use HasFactory;

    protected $table = 'corr_procesos_users';

    protected $fillable = [
        'user_id',
        'proceso_id',
        'detalle',
        'activo',
    ];

    /**
     * Relación: usuario asignado
     */
    public function usuario()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Relación: proceso al que pertenece esta asignación
     */
    public function proceso()
    {
        return $this->belongsTo(Proceso::class, 'proceso_id');
    }
}