<?php

namespace App\Models\Reserva;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage;
use App\Models\Maestras\MaeTerceros;
use App\Models\Reserva\Res_inmueble;
use App\Models\Reserva\Res_status;
use App\Models\Reserva\Res_reserva_evidencia;

class Res_reserva extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'res_inmueble_id',
        'res_status_id',
        'user_id',
        'nid',
        'name_reserva',
        'fecha_solicitud',
        'fecha_inicio',
        'fecha_fin',
        'comentario_reserva',
        'celular',
        'celular_respaldo',
        'soporte_pago',
        'revision_user_id',
        'revision_fecha',
        'revision_comentario',
        'puntuacion_admin', //reseña admin
        'observacion_recibido', //reseña admin
        'fecha_recibido', //reseña admin
        'user_id_recibido', //reseña admin
        'retroalimentacion', //reseña asociado
        'fecha_retroalimentacion', //reseña asociado
        'puntuacion_asociado', //reseña asociado
    ];

    protected $casts = [
        'fecha_solicitud' => 'date',
        'fecha_inicio' => 'date',
        'fecha_fin' => 'date',
    ];

    public function res_inmueble()
    {
        return $this->belongsTo(Res_inmueble::class);
    }

    public function res_status()
    {
        return $this->belongsTo(Res_status::class);
    }

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

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function tercero()
    {
        return $this->belongsTo(MaeTerceros::class, 'nid', 'cod_ter');
    }

    public function comments()
    {
        return $this->hasMany(Res_reserva_evidencia::class, 'res_reserva_id', 'id');
    }
}
