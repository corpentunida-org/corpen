<?php

namespace App\Models\Correspondencia;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Casts\Attribute; // Importante para el Accesor/Mutator inteligente
use App\Models\User;
use App\Models\Correspondencia\Trd;
use App\Models\Correspondencia\Estado;
use App\Models\Correspondencia\FlujoDeTrabajo;
use App\Models\Maestras\MaeTerceros;
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
        'documento_arc'
    ];

    protected $casts = [
        'fecha_solicitud' => 'datetime',
        'es_confidencial' => 'boolean',
        'finalizado' => 'boolean',
    ];

    /**
     * Accesor y Mutator inteligente para documento_arc.
     * Garantiza retrocompatibilidad: convierte texto plano antiguo o JSON nuevo 
     * siempre a un array indexado al consultar, y lo guarda como JSON en la BD.
     */
    protected function documentoArc(): Attribute
    {
        return Attribute::make(
            get: function ($value) {
                if (empty($value)) {
                    return [];
                }

                // Intentar decodificar el valor por si es una estructura JSON (Nuevos registros)
                $decoded = json_decode($value, true);

                // Si es un JSON válido y da como resultado un array, lo retornamos directo
                if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                    return $decoded;
                }

                // Si no es un JSON válido, significa que es texto plano (Registros antiguos)
                // Lo envolvemos en un array para mantener la consistencia en vistas y controladores
                return [$value];
            },
            set: function ($value) {
                // Al persistir en la base de datos, si es un array lo convertimos a texto JSON
                return is_array($value) ? json_encode($value) : $value;
            }
        );
    }

    /**
     * Obtiene la URL temporal firmada desde el disco S3 de AWS.
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

    /**
     * Relación: Tercero Remitente
     */
    public function remitente()
    {
        return $this->belongsTo(MaeTerceros::class, 'remitente_id', 'cod_ter');
    }

    /**
     * Relación: Historial de procesos/gestiones
     */
    public function procesos()
    {
        return $this->hasMany(CorrespondenciaProceso::class, 'id_correspondencia', 'id_radicado')
                    ->orderBy('created_at', 'desc');
    }

    /**
     * Relación: Medio de Recepción
     */
    public function medioRecepcion()
    {
        return $this->belongsTo(MedioRecepcion::class, 'medio_recibido', 'id');
    }

    /**
     * Relación con la comunicación de salida (una correspondencia tiene una respuesta)
     */
    public function comunicacionSalida()
    {
        return $this->hasOne(ComunicacionSalida::class, 'id_correspondencia', 'id_radicado');
    }
}