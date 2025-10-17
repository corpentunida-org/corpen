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
        'client_id',                // ID del cliente asociado a la interacción
        'agent_id',                 // ID del agente o usuario que realizó la interacción
        'interaction_date',         // Fecha y hora en que ocurrió la interacción
        'interaction_channel',      // Canal de comunicación (ej: teléfono, email, chat, presencial)
        'interaction_type',         // Tipo de interacción (ej: contacto inicial, seguimiento, reclamo)
        'duration',                 // Duración de la interacción (en minutos, segundos u otro formato)
        'outcome',                  // Resultado de la interacción (ej: exitoso, pendiente, sin respuesta)
        'notes',                    // Notas o detalles adicionales sobre la interacción
        'parent_interaction_id',    // ID de la interacción relacionada o anterior (en caso de seguimiento)
        'next_action_date',         // Fecha programada para la próxima acción o seguimiento
        'next_action_type',         // Tipo de próxima acción (ej: llamada, reunión, envío de correo)
        'next_action_notes',        // Notas o detalles sobre la próxima acción
        'attachment_urls',          // URLs de archivos adjuntos relacionados a la interacción
        'interaction_url',          // URL del registro o recurso externo vinculado a la interacción
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

}
