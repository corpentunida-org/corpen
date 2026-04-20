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
    protected $fillable = ['client_id', 'agent_id', 'interaction_date', 'interaction_channel', 'interaction_type', 'duration', 'outcome', 'notes', 'parent_interaction_id', 'id_linea_de_obligacion', 'id_user_asignacion', 'cedula_quien_llama', 'nombre_quien_llama', 'celular_quien_llama', 'parentesco_quien_llama'];

    protected $casts = [
        'interaction_date' => 'datetime',
        'id_linea_de_obligacion' => 'array',
        'id_user_asignacion' => 'integer',
    ];

    public function getFile($nameFile)
    {
        $url = '#';

        // Si nameFile llega como un array (por un cast en el modelo), tomamos el primer elemento
        if (is_array($nameFile) && count($nameFile) > 0) {
            $nameFile = $nameFile[0];
        }

        if ($nameFile) {
            if (Storage::disk('s3')->exists($nameFile)) {
                $url = Storage::disk('s3')->temporaryUrl(
                    $nameFile,
                    now()->addMinutes(10), // Aumentado a 10 min por comodidad del usuario
                );
            }
        }
        return $url;
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

    /*public function lineaDeObligacion()
    {
        return $this->belongsTo(LineaCredito::class, 'id_linea_de_obligacion', 'id');
    }*/
    public function getLineasDetalleAttribute()
    {
        if (!$this->id_linea_de_obligacion || !is_array($this->id_linea_de_obligacion)) {
            return collect();
        }
        return LineaCredito::whereIn('id', $this->id_linea_de_obligacion)->select('id', 'nombre')->get();
    }

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
