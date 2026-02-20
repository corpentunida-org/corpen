<?php

namespace App\Models\Correspondencia;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\User;
use App\Models\Correspondencia\Trd;
use App\Models\Correspondencia\Estado;
use App\Models\Correspondencia\FlujoDeTrabajo;
use App\Models\Maestras\maeTerceros;
use Illuminate\Support\Facades\Storage;

class Correspondencia extends Model
{
    use HasFactory;

    protected $table = 'corr_correspondencia';

    protected $primaryKey = 'id_radicado';

    protected $keyType = 'string';

    public $incrementing = false;

    protected $fillable = [
        'id_radicado',
        'fecha_solicitud',
        'asunto',
        'es_confidencial',
        'medio_recibido',
        'remitente_id',
        'trd_id',
        'flujo_id',
        'estado_id',
        'usuario_id',
        'observacion_previa',
        'finalizado',
        'final_descripcion',
        'documento_arc',
    ];

    protected $casts = [
        'fecha_solicitud' => 'datetime',
        'es_confidencial' => 'boolean',
        'finalizado' => 'boolean',
    ];

    public function getFile ($nameFile)
    {
        $url = '#';
        if($nameFile) {
            if (Storage::disk('s3')->exists($nameFile)) {
                $url = Storage::disk('s3')->temporaryUrl(
                    $nameFile, now()->addMinutes(5)
                );
            }
        }
        return $url;
    }

    /**
     * Relación: TRD
     */
    public function trd()
    {
        return $this->belongsTo(Trd::class, 'trd_id', 'id_trd');
    }

    /**
     * Relación: flujo
     */
    public function flujo()
    {
        return $this->belongsTo(FlujoDeTrabajo::class, 'flujo_id');
    }

    /**
     * Relación: estado
     */
    public function estado()
    {
        return $this->belongsTo(Estado::class, 'estado_id');
    }

    /**
     * Relación: usuario responsable
     */
    public function usuario()
    {
        return $this->belongsTo(User::class, 'usuario_id');
    }
    public function remitente()
    {
        return $this->belongsTo(maeTerceros::class, 'remitente_id','cod_ter');
    }
    /**
     * Relación: Historial de procesos/gestiones
     */
    public function procesos()
    {
        // Añadimos orderBy para que el último proceso aparezca primero en el resumen
        return $this->hasMany(CorrespondenciaProceso::class, 'id_correspondencia', 'id_radicado')
                    ->orderBy('created_at', 'desc');
    }
    /**
     * Relación: Medio de Recepción
     * Relaciona 'medio_recibido' de esta tabla con el 'id' de corr_medio_recepcion
     */
    public function medioRecepcion()
    {
        // belongsTo(ModeloDestino, llave_foranea_local, llave_primaria_destino)
        return $this->belongsTo(MedioRecepcion::class, 'medio_recibido', 'id');
    }

    /**
     * Relación con la comunicación de salida (una correspondencia tiene una respuesta)
     */
    public function comunicacionSalida()
    {
        // id_correspondencia es la FK en corr_comunicaciones_salida
        // id_radicado es la PK en corr_correspondencia
        return $this->hasOne(ComunicacionSalida::class, 'id_correspondencia', 'id_radicado');
    }
}
