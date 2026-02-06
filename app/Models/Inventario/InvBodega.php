<?php

namespace App\Models\Inventario;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class InvBodega extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'inv_bodegas'; // [cite: 233]
    protected $fillable = ['nombre', 'descripcion'];

    public function activos()
    {
        return $this->hasMany(InvActivo::class, 'id_InvBodegas');
    }
}