<?php

namespace App\Models\Interacciones;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\User;

class IntMessage extends Model
{
    protected $table = 'int_messages';

    protected $fillable = [
        'conversation_id',
        'user_id',
        'body',
        'attachment',
        'parent_id'
    ];

    public function conversation(): BelongsTo
    {
        return $this->belongsTo(IntConversation::class, 'conversation_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(IntMessage::class, 'parent_id');
    }

    public function replies(): HasMany
    {
        return $this->hasMany(IntMessage::class, 'parent_id');
    }
}