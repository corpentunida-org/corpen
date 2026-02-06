<?php

namespace App\Models\Inventario;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class InvTipo extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'inv_tipos';
    protected $fillable = ['nombre', 'descripcion'];

    // Relación 1:N -> Un Tipo tiene muchas Líneas (ESTA FALTABA)
    public function lineas()
    {
        return $this->hasMany(InvLinea::class, 'id_InvTipos');
    }

    // Relación 1:N -> Un Tipo tiene muchos Subgrupos
    public function subgrupos()
    {
        return $this->hasMany(InvSubgrupo::class, 'id_InvTipos');
    }
}