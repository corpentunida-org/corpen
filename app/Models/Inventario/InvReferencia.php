<?php

namespace App\Models\Inventario;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Inventario\InvActivo;
use App\Models\Inventario\InvDetalleCompra; 
use App\Models\Inventario\InvSubgrupo; 
use App\Models\Inventario\InvMarca;
use App\Models\Inventario\InvBodega;



class InvReferencia extends Model
{
    use HasFactory;

    protected $table = 'inv_referencias';

    protected $fillable = [
        'referencia',
        'detalle',
        'id_InvSubGrupos', // Campo movido aquí
        'id_InvBodegas', // Campo movido aquí
        'id_InvMarcas', // Campo movido aquí
    ];

    /**
     * Relación con la Marca (Padre)
     */
    public function marca()
    {
        return $this->belongsTo(InvMarca::class, 'id_InvMarcas');
    }

    public function subgrupo()
    {
        return $this->belongsTo(InvSubgrupo::class, 'id_InvSubGrupos');
    }

    public function bodega()
    {
        return $this->belongsTo(InvBodega::class, 'id_InvBodegas');
    }

    public function detallesCompra()
    {
        return $this->hasMany(InvDetalleCompra::class, 'invReferencias_id');
    }

    public function activos()
    {
        return $this->hasMany(InvActivo::class, 'invReferencias_id');
    }
}