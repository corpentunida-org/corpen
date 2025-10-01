<?php

namespace App\Models\Soportes;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ScpTipo extends Model
{
    use HasFactory;

    protected $table = 'scp_tipos';

    protected $fillable = [
        'nombre',
        'descripcion',
        'id_categoria',
    ];

    // Relación con sub-tipos
    public function subTipos()
    {
        return $this->hasMany(ScpSubTipo::class, 'scp_tipo_id');
    }

    public function categoria()
    {
        return $this->belongsTo(ScpCategoria::class, 'id_categoria','id');
    }

}
