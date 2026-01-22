<?php

namespace App\Models\Inventario;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class InvLinea extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'inv_lineas';
    
    // Agregamos 'id_InvTipos' para que guarde el padre
    protected $fillable = ['nombre', 'descripcion', 'id_InvTipos'];

    // Relación: Una Línea pertenece a un Tipo
    public function tipo()
    {
        return $this->belongsTo(InvTipo::class, 'id_InvTipos');
    }

    public function subgrupos()
    {
        return $this->hasMany(InvSubgrupo::class, 'id_InvLineas');
    }

    // Relación: Una Línea tiene muchos Grupos (Nuevo)
    public function grupos()
    {
        return $this->hasMany(InvGrupo::class, 'id_InvLineas');
    }
}