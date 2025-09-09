<?php

namespace App\Models\Flujo;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;

class Task extends Model
{
    use HasFactory;

    protected $fillable = [
        'titulo',
        'descripcion',
        'estado',
        'prioridad',
        'fecha_limite',
        'user_id',
        'workflow_id'
    ];

    /**
     * Usuario responsable de la tarea
     */
    public function user() {
        return $this->belongsTo(User::class);
    }

    /**
     * Flujo al que pertenece la tarea
     */
    public function workflow() {
        return $this->belongsTo(Workflow::class);
    }

    /**
     * Historial de cambios de la tarea
     */
    public function histories() {
        return $this->hasMany(TaskHistory::class);
    }

    /**
     * Comentarios asociados a la tarea
     */
    public function comments() {
        return $this->hasMany(TaskComment::class);
    }
}
