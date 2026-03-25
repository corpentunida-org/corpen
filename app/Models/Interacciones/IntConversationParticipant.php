<?php

namespace App\Models\Interacciones;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\User;

class IntConversationParticipant extends Model
{
    protected $table = 'int_conversation_participants';

    public $timestamps = false;

    protected $fillable = [
        'conversation_id',
        'user_id',
        'role_id',
        'joined_at'
    ];

    protected $casts = [
        'joined_at' => 'datetime',
    ];

    public function conversation(): BelongsTo
    {
        return $this->belongsTo(IntConversation::class, 'conversation_id');
    }

    public function role(): BelongsTo
    {
        return $this->belongsTo(IntRole::class, 'role_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}