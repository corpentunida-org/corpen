<?php

namespace App\Models\Inventario;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class InvEstado extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'inv_estados'; // [cite: 180]
    protected $fillable = ['nombre'];

    public function activos()
    {
        return $this->hasMany(InvActivo::class, 'id_Estado');
    }
}