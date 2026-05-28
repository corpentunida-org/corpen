<?php

namespace App\Models\Seguros;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Seguros\SegNovedades;
use App\Models\Seguros\SegEstadosNovedad;
use App\Models\User;

class SegCambioEstadoNovedad extends Model
{
    use HasFactory;
    protected $table = 'SEG_CambioEstadoNovedad';
    public $timestamps = false;

    protected $fillable = ['novedad', 'estado', 'fechaInicio', 'fechaCierre', 'observaciones','user_created'];
    protected $casts = [
        'fechaCierre' => 'datetime',
        'fechaInicio' => 'datetime',
    ];

    public function estadosname()
    {
        return $this->belongsTo(SegEstadosNovedad::class, 'estado', 'id');
    }

    public function novedades()
    {
        return $this->belongsTo(SegNovedades::class, 'novedad', 'id');
    }

    public function userrelation()
    {
        return $this->belongsTo(User::class, 'user_created', 'id');
    }
}
