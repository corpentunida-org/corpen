<?php

namespace App\Models\Cinco;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

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
    ];
}