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
