<?php

namespace App\Models\Reserva;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Reserva\Res_inmueble_foto;

class Res_inmueble extends Model
{
    use HasFactory;
    protected $table = 'res_inmuebles';

    protected $fillable = ['name', 'description', 'address', 'city', 'ubicacion'];

    public function fotosrel()
    {
        return $this->hasMany(Res_inmueble_foto::class, 'res_inmueble_id');
    }
}
