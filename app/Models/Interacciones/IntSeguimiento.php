<?php

namespace App\Models\Interacciones;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;
use Illuminate\Support\Facades\Storage;

class IntSeguimiento extends Model
{
    use HasFactory;

    protected $table = 'int_seguimiento';

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

    protected $casts = [
        'next_action_date' => 'datetime',
        'attachment_urls'  => 'array', // Necesario para guardar arrays/JSON de archivos
    ];

    /**
     * Genera la URL temporal del archivo en S3
     */
    public function getFile($nameFile)
    {
        $url = '#';
        if ($nameFile) {
            if (Storage::disk('s3')->exists($nameFile)) {
                $url = Storage::disk('s3')->temporaryUrl($nameFile, now()->addMinutes(5));
            }
        }
        return $url;
    }

    // ------------------- RELACIONES -------------------
    
    public function interaction()
    {
        return $this->belongsTo(Interaction::class, 'id_interaction');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'agent_id');
    }

    public function assignedUser()
    {
        return $this->belongsTo(User::class, 'id_user_asignacion');
    }

    // NOTA: Renombrado a 'outcomeRelation' para no chocar con la columna 'outcome'
    public function outcomeRelation() 
    {
        return $this->belongsTo(IntOutcome::class, 'outcome');
    }

    public function nextAction()
    {
        return $this->belongsTo(IntNextAction::class, 'next_action_type');
    }
}