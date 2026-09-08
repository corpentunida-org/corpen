<?php

namespace App\Models\Sgrh;

use Illuminate\Database\Eloquent\Model;

class VacacionColectivaAlcance extends Model
{
    protected $table = 'sgrh_vacacion_colectiva_alcances';

    protected $fillable = [
        'vacacion_colectiva_id',
        'tipo',
        'referencia_id',
    ];

    public function vacacionColectiva()
    {
        return $this->belongsTo(VacacionColectiva::class, 'vacacion_colectiva_id');
    }
}
