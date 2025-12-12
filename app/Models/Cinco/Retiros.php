<?php

namespace App\Models\Cinco;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Cinco\TiposRetiros;

class Retiros extends Model
{
    use HasFactory;
    protected $table = 'RET_retiros';

    protected $fillable = [
        'cod_ter',
        'tipoRetiro',
        'observación',
        'fecInicialLiquidacion',
        'consecutivoDocumento',
        'beneficioAntiguedad',
        'fechaRetiro',
        'fechaUltimoAporte',
        'fechaInicialLiquidacion'
    ];
    protected $casts = [
        'fechaCreacion' => 'datetime',
        'fecInicialLiquidacion' => 'datetime',
        'fechaUltimoAporte' => 'datetime',
        'fecInicialLiquidacion' => 'datetime',
        'fecInicialLiquidacion' => 'datetime',
    ];

    public function tipoRetiroNom()
    {
        return $this->belongsTo(TiposRetiros::class, 'tipoRetiro', 'id');
    }
}