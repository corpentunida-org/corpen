<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ComaeExCli extends Model
{
    use HasFactory;
    protected $table = 'coMaeCli'; // Nombre de la tabla en la base de datos
    protected $primaryKey = 'cedula'; // Nombre de la clave primaria en la tabla
    public $incrementing = false; // Indica si la clave primaria es autoincrementable o no

    // Define las columnas de la tabla que deseas utilizar
    protected $fillable = [
        'cedula',
        'apellido',
        'nombre',
        'distrito_id',
        'direccion',
        'ciudad_id',
        'estado',
        'celular',
        'email',
        'fechaNacimiento',
        'observacion_familia',
        'observacion',
    ];

    /* public function distrito()
    {
        return $this->belongsTo(Distrito::class);
    } */

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
        return $this->belongsTo(Distritos::class,'distrito_id', 'cod_dist');
    }
}
