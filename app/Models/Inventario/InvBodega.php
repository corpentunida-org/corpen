<?php

namespace App\Models\Inventario;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Inventario\InvActivo;
use App\Models\Inventario\InvEstado;
use App\Models\Inventario\InvReferencia; 

class InvBodega extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'inv_bodegas';
    protected $fillable = ['nombre', 'descripcion'];

    /**
     * Relación con los Activos
     * Una Bodega tiene muchos Activos A TRAVÉS de las Referencias.
     */
    public function activos()
    {
        return $this->hasManyThrough(
            InvActivo::class, 
            InvReferencia::class,
            'id_InvBodegas',      // Llave foránea en la tabla inv_referencias
            'invReferencias_id',  // Llave foránea en la tabla inv_activos
            'id',                 // Llave local en inv_bodegas
            'id'                  // Llave local en inv_referencias
        );
    }

    /**
     * Relación con las Referencias
     * Una Bodega tiene muchas Referencias.
     */
    public function referencias()
    {
        return $this->hasMany(InvReferencia::class, 'id_InvBodegas');
    }

    public function estados()
    {
        return $this->hasMany(InvEstado::class, 'id_bodega');
    }
}