<?php

namespace App\Models\Interacciones;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class IntConversation extends Model
{
    protected $table = 'int_conversations';

    protected $fillable = [
        'workspace_id',
        'type',
        'visibility',
        'name',
        'chatable_type',
        'chatable_id'
    ];

    /**
     * Relación Polimórfica para seguimientos/interacciones.
     */
    public function chatable(): MorphTo
    {
        return $this->morphTo();
    }

    public function workspace(): BelongsTo
    {
        return $this->belongsTo(IntWorkspace::class, 'workspace_id');
    }

    public function participants(): HasMany
    {
        return $this->hasMany(IntConversationParticipant::class, 'conversation_id');
    }

    public function messages(): HasMany
    {
        return $this->hasMany(IntMessage::class, 'conversation_id');
    }
}