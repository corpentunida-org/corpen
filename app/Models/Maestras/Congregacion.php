<?php

namespace App\Models\Maestras;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Maestras\claseCongregacion;

class Congregacion extends Model
{
    use HasFactory;

    protected $table = 'Congregaciones';

    protected $fillable = [
        'codigo',
        'nombre',
        'estado',
        'clase',
        'municipio',
        'direccion',
        'telefono',
        'celular',
        'distrito',
        'apertura',
        'cierre',
        'observacion',
        'pastor',
    ];

public function claseCongregacion()
{
    return $this->belongsTo(ClaseCongregacion::class, 'clase', 'id');
}
}
