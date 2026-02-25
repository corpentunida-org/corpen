<?php

namespace App\Models\Inventario;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Inventario\InvReferencia;
use App\Models\Inventario\InvCompra;


class InvDetalleCompra extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'inv_detalle_compras'; 

    // Se agregaron 'detalle' e 'invReferencias_id'
    protected $fillable = [
        'id', 
        'cantidades', 
        'precio_unitario', 
        'detalle',
        'sub_total', 
        'id_InvCompras',
        'invReferencias_id'
    ];

    /**
     * Relación con la Compra principal
     */
    public function compra()
    {
        return $this->belongsTo(InvCompra::class, 'id_InvCompras');
    }

    /**
     * Relación con la Referencia (Producto/Item)
     * Asegúrate de que la clase InvReferencia exista y tenga el namespace correcto.
     */
    public function referencia()
    {
        return $this->belongsTo(InvReferencia::class, 'invReferencias_id');
    }
}