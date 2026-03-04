<?php

namespace App\Models\Inventario;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Inventario\InvActivo;
use App\Models\Inventario\InvBodega;

class InvEstado extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'inv_estados';

    protected $fillable = [
        'nombre',
        'detalle',
        'id_bodega'
    ];

    /**
     * Relación: Un Estado tiene muchos Activos
     */
    public function activos()
    {
        return $this->hasMany(InvActivo::class, 'id_Estado');
    }

    /**
     * Relación: Un Estado pertenece a una Bodega
     */
    public function bodega()
    {
        return $this->belongsTo(InvBodega::class, 'id_bodega');
    }
}