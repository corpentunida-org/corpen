<?php

namespace App\Models\Interacciones;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class IntRole extends Model
{
    protected $table = 'int_roles';

    protected $fillable = [
        'name',
        'slug',
        'description'
    ];

    public function participants(): HasMany
    {
        return $this->hasMany(IntConversationParticipant::class, 'role_id');
    }
}