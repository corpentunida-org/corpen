<?php

namespace App\Models\Flujo;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;
use Carbon\Carbon;

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
     * Conversión de tipos de atributos.
     * Esto soluciona el error de format() en la vista.
     */
    protected $casts = [
        'fecha_limite' => 'date', 
        'created_at'   => 'datetime',
        'updated_at'   => 'datetime',
    ];

    /**
     * RELACIÓN AGREGADA (Soluciona el error "undefined relationship asignado")
     * Apunta al modelo User usando la clave foránea 'user_id'.
     */
    public function asignado() {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Relación estándar (alias de asignado, por si se usa en otras partes)
     */
    public function user() {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Flujo al que pertenece la tarea (Workflow de Origen)
     */
    public function workflow() {
        return $this->belongsTo(Workflow::class);
    }

    /**
     * Historial de cambios de la tarea (Log de Auditoría)
     */
    public function histories() {
        return $this->hasMany(TaskHistory::class)->latest();
    }

    /**
     * Comentarios asociados a la tarea (Feedback Colaborativo)
     */
    public function comments() {
        return $this->hasMany(TaskComment::class)->latest();
    }

    /**
     * Accesor Corporativo: Indica si la unidad de trabajo está vencida.
     */
    public function getEstaVencidaAttribute(): bool
    {
        if (!$this->fecha_limite) return false;
        return $this->fecha_limite->isPast() && $this->estado !== 'completado';
    }
}