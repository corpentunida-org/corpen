<?php

namespace App\Models\Soportes;

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
    ];

    // Relación con sub-tipos
    public function subTipos()
    {
        return $this->hasMany(ScpSubTipo::class, 'scp_tipo_id');
    }
}
