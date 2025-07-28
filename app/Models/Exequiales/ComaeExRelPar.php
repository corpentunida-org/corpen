<?php

namespace App\Models\Exequiales;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ComaeExRelPar extends Model
{
    use HasFactory;
    protected $table = 'EXE_ExRelPar';
    protected $primaryKey = 'cedula'; 
    public $timestamps = false;
    protected $fillable = [
        'idrow',
        'cedula',
        'nombre',
        'cod_par',
        'tipo',
        'fec_ing',
        'fec_nac',
        'estado',
        'cod_cli',
    ];

    public function asociado()
    {
        return $this->belongsTo(ComaeExCli::class, 'cedulaAsociado', 'cedula');
    }

    public function parentescoo()
    {
        return $this->belongsTo(Parentescos::class,'parentesco', 'codPar');
    }
}
