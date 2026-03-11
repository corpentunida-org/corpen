<?php

namespace App\Models\Interacciones;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use App\Models\Maestras\maeTerceros;
use App\Models\User;
use App\Models\Creditos\LineaCredito;
use Illuminate\Support\Facades\Storage;

class Interaction extends Model
{
    use HasFactory;

    // Actualizado exactamente a las columnas que indicaste que tiene tu tabla
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
        'id_linea_de_obligacion',
        'id_user_asignacion',
        'cedula_quien_llama',
        'nombre_quien_llama',
        'celular_quien_llama',
        'parentezco_quien_llama'
    ];

    protected $casts = [
        'attachment_urls'  => 'array',
        'interaction_date' => 'datetime',
        'next_action_date' => 'datetime',
        'id_linea_de_obligacion' => 'integer',
        'id_user_asignacion'     => 'integer',
    ];

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

    // ------------------- RELACIONES EXISTENTES -------------------
    public function agent()
    {
        return $this->belongsTo(User::class, 'agent_id', 'id');
    }
    
    public function client()
    {
        return $this->belongsTo(maeTerceros::class, 'client_id', 'cod_ter');
    }
    
    public function channel()
    {
        return $this->belongsTo(IntChannel::class, 'interaction_channel', 'id');
    }
    
    public function type()
    {
        return $this->belongsTo(IntType::class, 'interaction_type', 'id');
    }
    
    public function outcomeRelation()
    {
        return $this->belongsTo(IntOutcome::class, 'outcome', 'id');
    }
    
    public function nextAction()
    {
        return $this->belongsTo(IntNextAction::class, 'next_action_type', 'id');
    }

    // ------------------- NUEVAS RELACIONES CORREGIDAS -------------------
    
    /**
     * Obtiene la línea de obligación asociada a la interacción.
     */
    public function lineaDeObligacion()
    {
        return $this->belongsTo(LineaCredito::class, 'id_linea_de_obligacion', 'id');
    }

    /**
     * Obtiene el usuario al que se le asignó (escaló) la interacción.
     * Reemplaza las antiguas relaciones de area y cargo.
     */
    public function usuarioAsignado()
    {
        return $this->belongsTo(User::class, 'id_user_asignacion', 'id');
    }
    
    // NOTA: Se eliminaron las relaciones de areaDeAsignacion(), cargo() y DistritoDeObligacion() 
    // porque las columnas correspondientes (id_area_de_asignacion, id_cargo, id_distrito_interaccion) 
    // ya NO existen en tu tabla de base de datos actual.
}