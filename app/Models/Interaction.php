<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Interaction extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'interactions'; // Laravel infiere 'interactions' de 'Interaction', pero es bueno ser explícito.

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'client_id',
        'agent_id',
        'interaction_date',
        'interaction_channel',
        'interaction_type',
        'duration',
        'outcome',
        'notes',
        'parent_interaction_id',
        'next_action_date',
        'next_action_type',
        'next_action_notes',
        'attachment_urls', // Este campo se llenará con un array JSON
        'interaction_url',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'interaction_date' => 'datetime',
        'next_action_date' => 'datetime',
        'attachment_urls' => 'array', // ¡Importante! Esto le dice a Laravel que maneje este campo como un array/JSON
    ];




/*     public function client()
    {
        return $this->belongsTo(Client::class);
    }

    public function agent()
    {
        return $this->belongsTo(Agent::class);
    } */

    /**
     * Get the parent interaction that this interaction belongs to.
     *
     * Este es un ejemplo de relación para seguir el hilo de interacciones.
     *
     * public function parentInteraction()
     * {
     *     return $this->belongsTo(Interaction::class, 'parent_interaction_id');
     * }
     */

    /**
     * Get the child interactions for this interaction.
     *
     * Una interacción puede tener múltiples interacciones hijas (seguimientos).
     *
     * public function childInteractions()
     * {
     *     return $this->hasMany(Interaction::class, 'parent_interaction_id');
     * }
     */
}