<?php

namespace App\Models\Indicators;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Archivo\GdoEmpleado;
use App\Models\Archivo\GdoArea;
use App\Models\Role;

class IndIndicadores extends Model
{
    use HasFactory;

    protected $table = 'Ind_Indicadores';

    protected $fillable = ['nombre', 'calculo', 'meta', 'frecuencia', 'responsable', 'area', 'consulta_bd'];

    public function arearel()
    {
        return $this->belongsTo(Role::class,'area','id');
    }
}
