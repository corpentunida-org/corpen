<?php

namespace App\Models\Inventario;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Inventario\InvDetalleCompra;
use App\Models\Inventario\InvActivo;

class InvReferencia extends Model
{
    use HasFactory;

    // Indicamos explícitamente la tabla
    protected $table = 'inv_referencias';

    // Campos permitidos para asignación masiva
    protected $fillable = [
        'nombre',
        'detalle',
    ];

    /**
     * Relación con los Detalles de Compra
     * Una Referencia (producto/ítem) puede estar presente en muchos detalles de compra.
     */
    public function detallesCompra()
    {
        // El segundo parámetro es la llave foránea en la tabla 'inv_detalle_compras'
        // El tercer parámetro es la llave local en esta tabla 'inv_referencias'
        return $this->hasMany(InvDetalleCompra::class, 'invReferencias_id', 'id');
    }

    /**
     * Relación con los Activos
     * Una Referencia puede tener asociados muchos activos físicos.
     */
    public function activos()
    {
        // El segundo parámetro es la llave foránea en la tabla 'inv_activos'
        // El tercer parámetro es la llave local en esta tabla 'inv_referencias'
        return $this->hasMany(InvActivo::class, 'invReferencias_id', 'id');
    }
}