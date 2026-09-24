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
        'attachment_size',
        'interaction_url',
    ];

    protected $casts = [
        'next_action_date' => 'datetime',
        // OJO antes de "corregir" esto: el cast 'array' SÍ hace falta para leer bien lo ya
        // guardado. Las 3 vías que suben un adjunto siempre guardan UNA sola ruta (nunca un
        // array real), pero el propio cast 'array' la codifica con json_encode() al guardar —
        // así que en la columna queda literalmente '"corpentunida\/daytrack\/3\/x.png"' (con
        // comillas y barras escapadas), no la ruta limpia. Sin este cast, leer attachment_urls
        // devuelve ese texto crudo con comillas en vez de la ruta real (se probó: pasar esto a
        // 'string' rompía el link de los 13.440 seguimientos que ya tienen archivo). Con el
        // cast puesto, el round-trip (encode al guardar / decode al leer) sí funciona bien y
        // siempre devuelve un string limpio (nunca llega a devolver un array de verdad, porque
        // nadie guarda más de un archivo por seguimiento) — por eso destroy() en
        // InteractionController igual se defiende con (array) antes de iterar, por si acaso.
        'attachment_urls'  => 'array',
    ];

    /**
     * Genera la URL temporal del archivo en S3. Ya no verifica Storage::exists() antes: esa
     * llamada viajaba a S3 por cada fila con adjunto al listar (bloqueante, una por una) — con
     * miles de seguimientos la pantalla se volvía cada vez más lenta. destroy() en
     * InteractionController ya borra el archivo de S3 cuando se borra la interacción, así que
     * una ruta guardada casi siempre existe; en el raro caso de que no, el link generado
     * simplemente da error al abrirlo, lo cual es preferible a frenar toda la lista por archivo.
     */
    public function getFile($nameFile)
    {
        // attachment_urls está casteado a 'array' (ver el porqué arriba) pero en la práctica
        // siempre es un string ya decodificado — is_array() es solo por si alguna vez alguien
        // sí llega a guardar más de uno.
        if (is_array($nameFile) && count($nameFile) > 0) {
            $nameFile = $nameFile[0];
        }

        return $nameFile ? Storage::disk('s3')->temporaryUrl($nameFile, now()->addMinutes(5)) : '#';
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