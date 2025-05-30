<?php

namespace App\Models\Exequiales;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ComaeExRelPar extends Model
{
    use HasFactory;
    //protected $table = 'coMaeExRelPar';
    protected $table = 'EXE_CoMae_ExRelPar';
    protected $primaryKey = 'cedula'; 
    public $timestamps = false;
    protected $fillable = [
        'cedula',
        'nombre',
        'parentesco',
        'cedulaAsociado',
        'fechaNacimiento',
        'fechaIngreso',
    ];
    /*
        idrow (int)
        cod_cli (15)
        nombre (50)
        edad (numeric 3,0)
        cod_par (2)
        tipo (1)
        fec_ing (datetime)
        cedula (15)
        fec_nac(datetime)
    */

    public function asociado()
    {
        return $this->belongsTo(ComaeExCli::class, 'cedulaAsociado', 'cedula');
    }

    public function parentescoo()
    {
        return $this->belongsTo(Parentescos::class,'parentesco', 'codPar');
    }
}
