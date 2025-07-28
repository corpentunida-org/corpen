<?php

namespace App\Models\Exequiales;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ComaeExCli extends Model
{
    use HasFactory;
    protected $table = 'EXE_ExCli'; 
    protected $primaryKey = 'cod_cli';

    protected $fillable = [
        'idrow',
        'cod_cli',
        'benef',
        'cod_plan',
        'fec_ing',
        'cod_cco',
        'estado',
        'fec_ini',
        'por_descto',
        'contrato',
    ];


    public function beneficiarios()
    {
        return $this->hasMany(ComaeExRelPar::class, 'cedulaAsociado', 'cedula');
    }

    public function ciudade()
    {
        return $this->belongsTo(Ciudades::class, 'ciudad_id', 'codigo');
    }

    public function distrito()
    {
        return $this->belongsTo(Distritos::class, 'distrito_id', 'cod_dist');
    }
}
