<?php

namespace App\Models\Flujo;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;

class TaskHistory extends Model
{
    use HasFactory;

    /**
     * Definición de la tabla (opcional si sigue la convención, 
     * pero útil para claridad en flujos complejos)
     */
    protected $table = 'wor_task_histories';

    protected $fillable = [
        'task_id',
        'estado_anterior',
        'estado_nuevo',
        'cambiado_por',
        'fecha_cambio'
    ];

    /**
     * Conversión de tipos.
     * Crucial para evitar el error "format() on string" en las vistas.
     */
    protected $casts = [
        'fecha_cambio' => 'datetime',
        'created_at'   => 'datetime',
        'updated_at'   => 'datetime',
    ];

    /**
     * Relación: Tarea asociada al evento de auditoría.
     * Permite acceder a $history->task->titulo
     */
    public function task() 
    {
        return $this->belongsTo(Task::class);
    }

    /**
     * Relación: Usuario que realizó la transición de estado.
     * Se especifica 'cambiado_por' como la llave foránea.
     */
    public function user() 
    {
        return $this->belongsTo(User::class, 'cambiado_por');
    }

    /**
     * Boot Method del Modelo.
     * Automatiza la asignación de la fecha de cambio si no se provee.
     */
    protected static function booted()
    {
        static::creating(function ($history) {
            if (!$history->fecha_cambio) {
                $history->fecha_cambio = now();
            }
        });
    }
}