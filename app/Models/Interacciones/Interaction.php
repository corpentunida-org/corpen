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

class Interaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'client_id',                // 1) ID del cliente asociado a la interacción
        'agent_id',                 // 2) ID del agente o usuario que realizó la interacción
        'interaction_date',         // 3) Fecha y hora en que ocurrió la interacción
        'interaction_channel',      // 4)* Canal de comunicación (ej: teléfono, email, chat, presencial)
        'interaction_type',         // 5)* Tipo de interacción (ej: contacto inicial, seguimiento, reclamo)
        'duration',                 // 6)) Duración de la interacción (en minutos, segundos u otro formato)
        'outcome',                  // 7)* Resultado de la interacción (ej: exitoso, pendiente, sin respuesta)
        'notes',                    // 8) Notas o detalles adicionales sobre la interacción

        'parent_interaction_id',    // *** ID de la interacción relacionada o anterior (en caso de seguimiento)

        'next_action_date',         // 9) Fecha programada para la próxima acción o seguimiento
        'next_action_type',         // 10)* Tipo de próxima acción (ej: llamada, reunión, envío de correo)
        'next_action_notes',        // 11) Notas o detalles sobre la próxima acción 
        'parentezco_quien_llama',

        'cedula_quien_llama', 
        'nombre_quien_llama', 
        'celular_quien_llama', 

        'attachment_urls',          // 12) URLs de archivos adjuntos relacionados a la interacción
        'interaction_url',          // 13) URL del registro o recurso externo vinculado a la interacción

        //NUEVOS CAMPOS (AGREGADOS POR TI)
        'id_area',
        'id_cargo',
        'id_linea_de_obligacion',
        'id_area_de_asignacion',
        'id_funciones',
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

    // ------------------- RELACIONES EXISTENTES -------------------
    public function agent()
    {
        return $this->belongsTo(User::class, 'agent_id', 'nid');
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
    public function area()
    {
        return $this->belongsTo(GdoArea::class, 'id_area','id');
    }
    
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



    /**
     * Obtiene la función asociada a la interacción.
     */
    
/*     public function funcion()
    {
        return $this->belongsTo(GdoFuncione::class, 'id_funciones');
    }  */
}