<?php

namespace App\Models\Inventario;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Inventario\InvReferencia;
use App\Models\Inventario\InvActivo; 

class InvMarca extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'inv_marcas';
    protected $fillable = ['nombre', 'modelo', 'descripcion'];

    /**
     * Una Marca ahora pertenece a muchas Referencias.
     */
    public function referencias()
    {
        return $this->hasMany(InvReferencia::class, 'id_InvMarcas');
    }

    /**
     * Opcional: Si quieres llegar a los activos desde la marca, 
     * puedes usar HasManyThrough.
     */
    public function activos()
    {
        return $this->hasManyThrough(
            InvActivo::class, 
            InvReferencia::class, 
            'id_InvMarcas',      // FK en inv_referencias
            'invReferencias_id', // FK en inv_activos
            'id',                // Local key en inv_marcas
            'id'                 // Local key en inv_referencias
        );
    }
}