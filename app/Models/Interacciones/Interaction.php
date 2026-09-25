<?php

namespace App\Models\Interacciones;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Maestras\MaeTerceros;
use App\Models\User;
use App\Models\Creditos\LineaCredito;
use Illuminate\Support\Facades\Storage;
use App\Models\Cartera\CarComprobantePago;

class Interaction extends Model
{
    use HasFactory;
    protected $table = 'interactions';
    protected $fillable = ['client_id', 'agent_id', 'interaction_date', 'interaction_channel', 'interaction_type', 'duration', 'outcome', 'motivo_no_efectivo_id', 'notes', 'parent_interaction_id', 'id_linea_de_obligacion', 'id_user_asignacion', 'cedula_quien_llama', 'nombre_quien_llama', 'celular_quien_llama', 'parentesco_quien_llama'];

    protected $casts = [
        'interaction_date' => 'datetime',
        'id_linea_de_obligacion' => 'array',
        'id_user_asignacion' => 'integer',
    ];

    /**
     * Ya no verifica Storage::exists() antes de generar el link — esa llamada viajaba a S3 por
     * cada fila con adjunto al listar (bloqueante, una por una); con miles de seguimientos la
     * pantalla se volvía cada vez más lenta a medida que crecía el volumen. destroy() en
     * InteractionController ya borra el archivo de S3 cuando se borra la interacción, así que
     * una ruta guardada casi siempre existe; en el raro caso de que no, el link simplemente da
     * error al abrirlo, preferible a frenar toda la lista por archivo.
     */
    public function getFile($nameFile)
    {
        // Si nameFile llega como un array (por un cast legado en el modelo), tomamos el primer
        // elemento — attachment_urls en la práctica siempre guarda una sola ruta como texto.
        if (is_array($nameFile) && count($nameFile) > 0) {
            $nameFile = $nameFile[0];
        }

        return $nameFile ? Storage::disk('s3')->temporaryUrl($nameFile, now()->addMinutes(10)) : '#';
    }

    // ------------------- RELACIONES -------------------
    public function agent()
    {
        return $this->belongsTo(User::class, 'agent_id', 'id');
    }

    public function client()
    {
        return $this->belongsTo(MaeTerceros::class, 'client_id', 'cod_ter');
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

    public function motivoNoEfectivo()
    {
        return $this->belongsTo(IntMotivoNoEfectivo::class, 'motivo_no_efectivo_id');
    }

    public function lineaDeObligacion()
    {
        return $this->belongsTo(LineaCredito::class, 'id_linea_de_obligacion', 'id');
    }

    /* Se emplea en la vista show por eso lo descomente de nuevo ya que lo habia comentado para mejor rendimiento - requiere revision */
    public function getLineasDetalleAttribute()
    {
        if (empty($this->id_linea_de_obligacion)) {
            return collect();
        }

        return LineaCredito::whereIn('id',$this->id_linea_de_obligacion)
            ->select('id', 'nombre')->get();
    
    } 
    /* public function getLineasIdsAttribute() {
        return is_array($this->id_linea_de_obligacion) ? $this->id_linea_de_obligacion : [];
    } */

    public function usuarioAsignado()
    {
        return $this->belongsTo(User::class, 'id_user_asignacion', 'id');
    }

    public function seguimientos()
    {
        return $this->hasMany(IntSeguimiento::class, 'id_interaction');
    }
    public function comprobantes()
    {
        return $this->hasMany(CarComprobantePago::class, 'id_interaction', 'id');
    }
}
