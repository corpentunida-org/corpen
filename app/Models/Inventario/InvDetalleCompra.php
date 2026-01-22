<?php

namespace App\Models\Inventario;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class InvDetalleCompra extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'inv_detalle_compras'; // [cite: 387]
    protected $fillable = [
        'cantidades', 'precio_unitario', 'sub_total', 'id_InvCompras'
    ];

    public function compra()
    {
        return $this->belongsTo(InvCompra::class, 'id_InvCompras');
    }
}