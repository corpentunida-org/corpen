<?php

namespace App\Models\Inventario;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class InvMetodo extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'inv_metodos';
    protected $fillable = ['nombre'];

    // Relación: Un método tiene muchas compras
    public function compras()
    {
        return $this->hasMany(InvCompra::class, 'id_InvMetodos');
    }
}