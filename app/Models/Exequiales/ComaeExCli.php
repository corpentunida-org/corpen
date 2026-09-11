<?php

namespace App\Models\Exequiales;

use App\Models\Maestras\MaeTerceros;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ComaeExCli extends Model
{
    use HasFactory;
    protected $table = 'EXE_ExCli';
    protected $primaryKey = 'cod_cli';
    public $incrementing = false;
    public $timestamps = true;

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
        return $this->hasMany(ComaeExRelPar::class, 'cod_cli', 'cod_cli');
    }

    public function tercero()
    {
        return $this->belongsTo(MaeTerceros::class, 'cod_cli', 'cod_ter');
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
