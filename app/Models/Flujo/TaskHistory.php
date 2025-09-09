<?php

namespace App\Models\Flujo;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;

class TaskHistory extends Model
{
    use HasFactory;

    protected $fillable = [
        'task_id',
        'estado_anterior',
        'estado_nuevo',
        'cambiado_por',
        'fecha_cambio'
    ];

    /**
     * Tarea asociada al historial
     */
    public function task() {
        return $this->belongsTo(Task::class);
    }

    /**
     * Usuario que realizó el cambio
     */
    public function user() {
        return $this->belongsTo(User::class, 'cambiado_por');
    }
}
