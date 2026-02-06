<?php

namespace App\Models\Inventario;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class InvGrupo extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'inv_grupos';
    
    protected $fillable = ['nombre', 'descripcion', 'id_InvLineas'];

    // Relación: Un Grupo pertenece a una Línea
    public function linea()
    {
        return $this->belongsTo(InvLinea::class, 'id_InvLineas');
    }

    // Relación: Un Grupo tiene muchos Subgrupos
    public function subgrupos()
    {
        return $this->hasMany(InvSubgrupo::class, 'id_InvGrupos');
    }
}