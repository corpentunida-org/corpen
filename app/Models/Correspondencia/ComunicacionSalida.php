<?php

namespace App\Models\Correspondencia;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\User;

class ComunicacionSalida extends Model
{
    use HasFactory;

    protected $table = 'corr_comunicaciones_salida';

    protected $primaryKey = 'id_respuesta';

    protected $fillable = [
        'id_correspondencia',
        'nro_oficio_salida',
        'cuerpo_carta',
        'ruta_pdf',
        'fecha_generacion',
        'estado_envio',
        'id_plantilla',
        'fk_usuario',
    ];

    protected $casts = [
        'fecha_generacion' => 'datetime',
    ];

    /**
     * Correspondencia origen
     */
    public function correspondencia()
    {
        return $this->belongsTo(Correspondencia::class, 'id_correspondencia', 'id_radicado');
    }

    /**
     * Plantilla usada
     */
    public function plantilla()
    {
        return $this->belongsTo(Plantilla::class, 'id_plantilla');
    }

    /**
     * Usuario generador
     */
    public function usuario()
    {
        return $this->belongsTo(User::class, 'fk_usuario');
    }
}
