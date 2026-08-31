<?php

namespace App\Models\Maestras;

use Illuminate\Database\Eloquent\Model;

class MaeDepartamento extends Model
{
    protected $table = 'MaeDepartamentos';

    public $timestamps = false;

    protected $primaryKey = 'codigo_Dane';

    public $incrementing = false;

    protected $keyType = 'integer';

    public function municipios()
    {
        return $this->hasMany(MaeMunicipios::class, 'id_departamento', 'codigo_Dane');
    }
}
