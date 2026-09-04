<?php

namespace App\Models\Sgrh;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;

class ContratoModificacion extends Model
{
    protected $table = 'sgrh_contrato_modificaciones';

    protected $fillable = [
        'contrato_id',
        'causal',
        'observacion',
        // "Foto" del contrato (tipo, cargo, área, fechas, salario, estado, documento) tal como
        // quedó vigente a partir de este evento — permite ver/imprimir el contrato como era en
        // cualquier punto de su historia, no solo su estado actual.
        'snapshot',
        'user_id',
    ];

    protected $casts = [
        'snapshot' => 'array',
    ];

    public function contrato()
    {
        return $this->belongsTo(Contrato::class, 'contrato_id');
    }

    public function usuario()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
