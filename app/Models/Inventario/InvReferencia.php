<?php

namespace App\Models\Inventario;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Inventario\InvDetalleCompra;
use App\Models\Inventario\InvActivo;
use App\Models\Inventario\InvSubgrupo;
use App\Models\Inventario\InvBodega; 

class InvReferencia extends Model
{
    use HasFactory;

    protected $table = 'inv_referencias';

    protected $fillable = [
        'referencia',
        'detalle',
        'id_InvSubGrupos',
        'id_InvBodegas',
    ];

    /**
     * Relación con el Subgrupo (Padre)
     * Una Referencia pertenece a un Subgrupo.
     */
    public function subgrupo()
    {
        return $this->belongsTo(InvSubgrupo::class, 'id_InvSubGrupos');
    }

    /**
     * Relación con la Bodega (Padre)
     * Una Referencia pertenece a una Bodega.
     */
    public function bodega()
    {
        // 'id_InvBodegas' es la llave foránea en esta tabla (inv_referencias)
        return $this->belongsTo(InvBodega::class, 'id_InvBodegas');
    }

    /**
     * Relación con los Detalles de Compra
     */
    public function detallesCompra()
    {
        return $this->hasMany(InvDetalleCompra::class, 'invReferencias_id', 'id');
    }

    /**
     * Relación con los Activos
     */
    public function activos()
    {
        return $this->hasMany(InvActivo::class, 'invReferencias_id', 'id');
    }
}