<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Maestras\maeTerceros;

class Interaction extends Model
{
    use HasFactory;

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
        'attachment_urls',
        'interaction_url',
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


}
