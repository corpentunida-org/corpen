<?php

namespace App\Models\Interacciones;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;

class IntSeguimiento extends Model
{
    use HasFactory;

    // Nombre de la tabla definido en la migración
    protected $table = 'int_seguimiento';

    // Campos que se pueden llenar masivamente
    protected $fillable = [
        'id_interaction',
        'agent_id',
        'id_user_asignacion',
        'outcome',
        'next_action_type',
        'next_action_date',
        'next_action_notes',
        'attachment_urls',
        'interaction_url',
    ];

    // Cast de fechas para que Laravel las trate como objetos Carbon
    protected $casts = [
        'next_action_date' => 'datetime',
    ];

    /**
     * Relación con la Interacción principal (Padre)
     */
    public function interaction()
    {
        return $this->belongsTo(Interaction::class, 'id_interaction');
    }

    /**
     * Relación con el Agente que creó el registro
     */
    public function creator()
    {
        return $this->belongsTo(User::class, 'agent_id');
    }

    /**
     * Relación con el Usuario asignado para la gestión
     */
    public function assignedUser()
    {
        return $this->belongsTo(User::class, 'id_user_asignacion');
    }

    /**
     * Relación con el Resultado de la interacción
     */
    public function outcome()
    {
        // Apunta al modelo IntOutcome que me mostraste antes
        return $this->belongsTo(IntOutcome::class, 'outcome');
    }

    /**
     * Relación con el Tipo de Acción Siguiente
     */
    public function nextAction()
    {
        // Apunta al modelo IntNextAction que me mostraste antes
        return $this->belongsTo(IntNextAction::class, 'next_action_type');
    }
}