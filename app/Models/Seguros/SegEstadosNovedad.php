<?php

namespace App\Models\Seguros;


use Illuminate\Database\Eloquent\Model;

class SegEstadosNovedad extends Model
{
    protected $table = 'SEG_estadosNovedad';
    protected $fillable = [
        'novedad',
        'estado',
        'fechaIncio',
        'estado',
        'fechaCierre',
        'observaciones',
    ];

    public $timestamps = false;

    public function novedad()
    {
        return $this->hasMany(SegNovedades::class, 'estado', 'id');
    }

}