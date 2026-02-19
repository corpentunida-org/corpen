<?php

namespace App\Models\Correspondencia;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\Storage;
use App\Models\User;

class CorrespondenciaProceso extends Model
{
    use HasFactory;

    protected $table = 'corr_correspondencia_proceso';

    protected $fillable = [
        'id',
        'id_correspondencia',
        'observacion',
        'estado',
        'id_proceso',
        'notificado_email',
        'fecha_gestion',
        'documento_arc',
        'finalizado',
        'fk_usuario',
        'created_at',
        'updated_at',
    ];

    protected $casts = [
        'notificado_email' => 'boolean',
        'finalizado' => 'boolean',
        'fecha_gestion' => 'datetime',
        'documento_arc' => 'array', // <--- NUEVO: Cast automático a Array/JSON
    ];

    /**
     * Obtiene una lista con las URLs temporales de todos los archivos guardados
     */
    public function getArchivosUrls()
    {
        $urls = [];
        $archivos = $this->documento_arc;

        if (is_array($archivos)) {
            foreach ($archivos as $archivo) {
                if ($archivo && Storage::disk('s3')->exists($archivo)) {
                    $urls[] = [
                        'nombre' => basename($archivo), // Saca el nombre del archivo de la ruta
                        'url'    => Storage::disk('s3')->temporaryUrl($archivo, now()->addMinutes(5))
                    ];
                }
            }
        } elseif (is_string($archivos) && $archivos) {
            // Por si tienes registros antiguos guardados como un solo string
            if (Storage::disk('s3')->exists($archivos)) {
                $urls[] = [
                    'nombre' => basename($archivos),
                    'url'    => Storage::disk('s3')->temporaryUrl($archivos, now()->addMinutes(5))
                ];
            }
        }

        return $urls;
    }

    /**
     * Correspondencia relacionada
     */
    public function correspondencia()
    {
        return $this->belongsTo(Correspondencia::class, 'id_correspondencia', 'id_radicado');
    }

    /**
     * Proceso asociado
     */
    public function proceso()
    {
        return $this->belongsTo(Proceso::class, 'id_proceso');
    }

    /**
     * Usuario responsable
     */
    public function usuario()
    {
        return $this->belongsTo(User::class, 'fk_usuario');
    }
}