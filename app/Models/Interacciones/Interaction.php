<?php

namespace App\Models\Interacciones;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use App\Models\Maestras\maeTerceros;
use App\Models\User;
// Asumimos que existen estos modelos para las nuevas relaciones
use App\Models\Archivo\GdoArea;
use App\Models\Archivo\GdoCargo;
// use App\Models\Archivo\GdoFuncione;
use App\Models\Creditos\LineaCredito;
use App\Models\Maestras\maeDistritos;
use Illuminate\Support\Facades\Storage;

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
        'id_linea_de_obligacion',
        'id_user_asignacion',
        'cedula_quien_llama',
        'nombre_quien_llama',
        'celular_quien_llama',
        'parentezco_quien_llama'
    ];

    protected $casts = [
        'attachment_urls'   => 'array',
        'interaction_date'  => 'datetime',
        'next_action_date'  => 'datetime',
        // --- CASTS PARA LOS NUEVOS CAMPOS ---
        'id_area'               => 'integer',
        'id_cargo'              => 'integer',
        'id_linea_de_obligacion'=> 'integer',
        'id_area_de_asignacion' => 'integer',
        'id_funciones'          => 'integer',
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
        return $this->belongsTo(IntChannel::class, 'interaction_channel','id');
    }
    public function type()
    {
        return $this->belongsTo(IntType::class, 'interaction_type','id');
    }
    public function outcomeRelation()
    {
        return $this->belongsTo(IntOutcome::class, 'outcome','id');
    }
    public function nextAction()
    {
        return $this->belongsTo(IntNextAction::class, 'next_action_type','id');
    }

    // ------------------- NUEVAS RELACIONES -------------------
    /**
     * Obtiene el área asociada a la interacción.
     */
    
    
    /**
     * Obtiene el área de asignación asociada a la interacción.
     */
    public function areaDeAsignacion()
    {
        return $this->belongsTo(GdoArea::class, 'id_area_de_asignacion','id');
    }

    /**
     * Obtiene el cargo asociado a la interacción.
     */
    public function cargo()
    {
        return $this->belongsTo(GdoCargo::class, 'id_cargo','id');
    }

    /**
     * Obtiene la línea de obligación asociada a la interacción.
     */
    public function lineaDeObligacion()
    {
        return $this->belongsTo(LineaCredito::class, 'id_linea_de_obligacion','id');
    }
    public function DistritoDeObligacion()
    {
        return $this->belongsTo(maeDistritos::class, 'id_distrito_interaccion','COD_DIST');
    }


    /**
     * Obtiene la función asociada a la interacción.
     */
    
/*     public function funcion()
    {
        return $this->belongsTo(GdoFuncione::class, 'id_funciones');
    }  */
}