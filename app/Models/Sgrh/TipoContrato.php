<?php

namespace App\Models\Sgrh;

use Illuminate\Database\Eloquent\Model;

class TipoContrato extends Model
{
    protected $table = 'sgrh_tipos_contrato';

    protected $fillable = ['nombre', 'activo'];

    protected $casts = [
        'activo' => 'boolean',
    ];

    public function contratos()
    {
        return $this->hasMany(Contrato::class, 'tipo_contrato_id');
    }
}
