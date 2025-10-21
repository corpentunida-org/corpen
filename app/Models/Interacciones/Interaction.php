<?php

namespace App\Models\Interacciones;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use App\Models\Maestras\maeTerceros;
use App\Models\User;        

class Interaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'client_id',                // 1) ID del cliente asociado a la interacción
        'agent_id',                 // 2) ID del agente o usuario que realizó la interacción
        'interaction_date',         // 3) Fecha y hora en que ocurrió la interacción
        'interaction_channel',      // 4)* Canal de comunicación (ej: teléfono, email, chat, presencial)
        'interaction_type',         // 5)* Tipo de interacción (ej: contacto inicial, seguimiento, reclamo)
        'duration',                 // 6)) Duración de la interacción (en minutos, segundos u otro formato)
        'outcome',                  // 7)* Resultado de la interacción (ej: exitoso, pendiente, sin respuesta)
        'notes',                    // 8) Notas o detalles adicionales sobre la interacción


        'parent_interaction_id',    // *** ID de la interacción relacionada o anterior (en caso de seguimiento)
                                    // Area asignada de la interaccion
                                    // Si el area es Cartera - Relacion con las lineas de creditos.

        'next_action_date',         // 9) Fecha programada para la próxima acción o seguimiento
        'next_action_type',         // 10)* Tipo de próxima acción (ej: llamada, reunión, envío de correo)
        'next_action_notes',        // 11) Notas o detalles sobre la próxima acción

        'attachment_urls',          // 12) URLs de archivos adjuntos relacionados a la interacción
        'interaction_url',          // 13) URL del registro o recurso externo vinculado a la interacción
    ];


    protected $casts = [
        'attachment_urls'   => 'array',
        'interaction_date'  => 'datetime',
        'next_action_date'  => 'datetime',
    ];
    // Relación con el agente (usuario)
    public function agent()
    {
        return $this->belongsTo(User::class, 'agent_id');
    }
    public function client()
    {
        return $this->belongsTo(maeTerceros::class, 'client_id', 'cod_ter');
    }
    public function channel()
    {
        return $this->belongsTo(IntChannel::class, 'interaction_channel');
    }
//
    public function type()
    {
        return $this->belongsTo(IntType::class, 'interaction_type');
    }
    public function outcomeRelation()
    {
        return $this->belongsTo(IntOutcome::class, 'outcome');
    }
    public function nextAction()
    {
        return $this->belongsTo(IntNextAction::class, 'next_action_type');
    }

}
