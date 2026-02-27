<?php

namespace App\Models\Inventario;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Inventario\InvActivo;
use App\Models\Inventario\InvReferencia; 

class InvBodega extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'inv_bodegas';
    protected $fillable = ['nombre', 'descripcion'];

    /**
     * Relación con los Activos
     */
    public function activos()
    {
        return $this->hasMany(InvActivo::class, 'id_InvBodegas');
    }

    /**
     * Relación con las Referencias
     * Una Bodega tiene muchas Referencias.
     */
    public function referencias()
    {
        return $this->hasMany(InvReferencia::class, 'id_InvBodegas');
    }
}